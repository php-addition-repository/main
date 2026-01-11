#!/usr/bin/env bash
# Computes which packages to push:
# - For ref_type=branch: only changed packages (unless PUSH_ONLY_CHANGED_ON_MAIN=false)
# - For ref_type=tag: all packages
#
# Usage: plan_packages.sh <ref_type:branch|tag> <before_sha|-> <head_sha> <packages_json_path>
# Env:   PUSH_ONLY_CHANGED_ON_MAIN=true|false (default true)
#
# Outputs: JSON array of selected package objects to stdout.

set -euo pipefail

# --- utilities ---
require_cmd() { command -v "$1" >/dev/null 2>&1 || { echo "ERROR: '$1' not found" >&2; exit 1; }; }
require_cmd jq
require_cmd git

REF_TYPE="${1:-}"
BEFORE_ARG="${2:-}"
HEAD_SHA="${3:-}"
PACKAGES_JSON_PATH="${4:-}"

PUSH_ONLY_CHANGED_ON_MAIN="${PUSH_ONLY_CHANGED_ON_MAIN:-true}"

if [ -z "$REF_TYPE" ] || [ -z "$HEAD_SHA" ] || [ -z "$PACKAGES_JSON_PATH" ]; then
  echo "Usage: $0 <ref_type:branch|tag> <before_sha|-> <head_sha> <packages_json_path>" >&2
  exit 1
fi

if [ ! -f "$PACKAGES_JSON_PATH" ]; then
  echo "ERROR: packages_json_path '$PACKAGES_JSON_PATH' not found." >&2
  exit 1
fi

# Normalize BEFORE
BEFORE="$BEFORE_ARG"
if [ "$BEFORE" = "-" ]; then
  BEFORE=""
fi

# Load packages JSON as compact lines so we can read them safely
# shellcheck disable=SC2013
PKG_LINES="$(jq -c '.[]' "$PACKAGES_JSON_PATH" || true)"

if [ -z "$PKG_LINES" ]; then
  # No packages — emit empty array
  echo "[]"
  exit 0
fi

# --- compute changed files (only needed for branch mode with selective push) ---
CHANGED_FILES=""
if [ "$REF_TYPE" = "branch" ] && [ "$PUSH_ONLY_CHANGED_ON_MAIN" = "true" ]; then
  # Resolve initial push edge case
  if [ -z "$BEFORE" ] || echo "$BEFORE" | grep -Eq '^0{40}$'; then
    # Find the root commit of current HEAD
    BEFORE="$(git rev-list --max-parents=0 "$HEAD_SHA" | tail -n 1)"
  fi

  # Collect changed files between BEFORE..HEAD_SHA
  # Use a newline-separated list to avoid bash arrays
  CHANGED_FILES="$(git diff --name-only "$BEFORE" "$HEAD_SHA" || true)"
fi

# --- select packages ---
# We build the output progressively in a temp file to keep it simple.
out_file="$(mktemp)"
printf '[' > "$out_file"
sep=""

# Iterate over packages line by line (portable)
echo "$PKG_LINES" | while IFS= read -r row; do
  # Extract fields via jq
  pkg_path="$(printf '%s' "$row" | jq -r '.path')"
  pkg_repo="$(printf '%s' "$row" | jq -r '.repo')"

  # Decide whether to include
  include="false"

  case "$REF_TYPE" in
    tag)
      # On tag pushes: include all packages
      include="true"
      ;;
    branch)
      if [ "$PUSH_ONLY_CHANGED_ON_MAIN" = "false" ]; then
        include="true"
      else
        # Include if any changed file is under the package path
        # Iterate lines of CHANGED_FILES; match prefix
        if [ -n "$CHANGED_FILES" ]; then
          # Use grep -E with ^pkg_path/ prefix, safely escaped
          # Ensure trailing slash to prevent partial matches
          escaped_path="$(printf '%s' "$pkg_path" | sed 's/[.[\^$*+?{|()]/\\&/g')"
          if printf '%s\n' "$CHANGED_FILES" | grep -E "^${escaped_path}(/|$)" >/dev/null 2>&1; then
            include="true"
          fi
        fi
      fi
      ;;
    *)
      echo "ERROR: Unknown ref_type '$REF_TYPE'" >&2
      exit 1
      ;;
  esac

  if [ "$include" = "true" ]; then
    printf '%s%s' "$sep" "$row" >> "$out_file"
    sep=","
  fi
done

printf ']\n' >> "$out_file"

cat "$out_file"
rm -f "$out_file"
