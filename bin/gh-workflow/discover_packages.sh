#!/usr/bin/env bash

set -euo pipefail

ORG="${1:-}"
ROOT="${2:-packages}"

if [[ -z "$ORG" ]]; then
  echo "Usage: $0 <github_org> [packages_dir]" >&2
  exit 1
fi

if [[ ! -d "$ROOT" ]]; then
  # No packages directory — return an empty array
  echo "[]"
  exit 0
fi

# Collect immediate subdirectories under ROOT
mapfile -t PKG_DIRS < <(find "$ROOT" -mindepth 1 -maxdepth 1 -type d -printf '%f\n' | sort)

if [[ ${#PKG_DIRS[@]} -eq 0 ]]; then
  echo "[]"
  exit 0
fi

# Build JSON array
{
  printf '['
  sep=''
  for name in "${PKG_DIRS[@]}"; do
    # Skip dot-prefixed folders
    if [[ "$name" == .* ]]; then
      continue
    fi
    path="$ROOT/$name"
    repo="$ORG/$name"
    printf '%s{"path":"%q","repo":"%q","default_branch":"main"}' "$sep" "$path" "$repo"
    sep=','
  done
  printf ']'
}
