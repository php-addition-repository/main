#!/usr/bin/env bash

set -euo pipefail

require_cmd() { command -v "$1" >/dev/null 2>&1 || { echo "ERROR: '$1' not found" >&2; exit 1; }; }
require_cmd jq
require_cmd git

SELECTED_JSON_PATH="${1:-}"
REF_TYPE="${2:-}"     # "branch" or "tag"
TAG_NAME="${3:-}"     # "-" or empty for branch pushes
HEAD_SHA="${4:-}"

TOKEN="${ACCESS_TOKEN:-}"
DRY_RUN="${DRY_RUN:-0}"

if [[ -z "$SELECTED_JSON_PATH" || -z "$REF_TYPE" || -z "$HEAD_SHA" ]]; then
  echo "Usage: $0 <selected_json_path> <ref_type:branch|tag> <tag_name|-> <head_sha>" >&2
  exit 1
fi
if [[ "$TAG_NAME" == "-" ]]; then TAG_NAME=""; fi
if [[ -z "$TOKEN" ]]; then
  echo "ERROR: ACCESS_TOKEN is not set." >&2
  exit 1
fi

# Configure identity (useful locally too)
git config user.name "${GIT_AUTHOR_NAME:-github-actions[bot]}"
git config user.email "${GIT_AUTHOR_EMAIL:-github-actions[bot]@users.noreply.github.com}"

mapfile -t SELECTED < <(jq -r '.[] | @base64' < "$SELECTED_JSON_PATH")

for row64 in "${SELECTED[@]}"; do
  row="$(echo "$row64" | base64 -d)"
  path="$(echo "$row" | jq -r '.path')"
  repo="$(echo "$row" | jq -r '.repo')"
  branch="main" # fixed

  base_url="https://github.com/${repo}.git"
  target_url="${base_url/https:\/\//https://${TOKEN}@}"

  safe_name="$(echo "$path" | sed 's#[^a-zA-Z0-9._-]#-#g')"
  split_branch="split-${safe_name}"

  echo "==> Splitting '$path' -> '$base_url' (branch: $branch)"
  git branch -D "$split_branch" 2>/dev/null || true

  if git subtree split --prefix="$path" "$HEAD_SHA" -b "$split_branch" >/dev/null 2>&1; then
    echo "    Pushing split to $base_url:$branch"
    if [[ "$DRY_RUN" == "1" ]]; then
      echo "DRY_RUN: git push --force-with-lease \"$target_url\" \"refs/heads/${split_branch}:refs/heads/${branch}\""
    else
      git push --force-with-lease "$target_url" "refs/heads/${split_branch}:refs/heads/${branch}"
    fi

    if [[ "$REF_TYPE" == "tag" && -n "$TAG_NAME" ]]; then
      echo "    Propagating tag '${TAG_NAME}'"
      if [[ "$DRY_RUN" == "1" ]]; then
        echo "DRY_RUN: git tag -f \"$TAG_NAME\" \"$split_branch\""
        echo "DRY_RUN: git push \"$target_url\" \"refs/tags/${TAG_NAME}:refs/tags/${TAG_NAME}\""
      else
        git tag -f "$TAG_NAME" "$split_branch"
        git push "$target_url" "refs/tags/${TAG_NAME}:refs/tags/${TAG_NAME}"
      fi
    fi

    git branch -D "$split_branch" >/dev/null
  else
    echo "WARNING: Subtree split failed or no history for '$path'. Skipping."
  fi
done

echo "All done."
