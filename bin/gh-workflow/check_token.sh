#!/usr/bin/env bash

set -euo pipefail

TOKEN="${ACCESS_TOKEN:-}"
if [[ -z "$TOKEN" ]]; then
  echo "ERROR: ACCESS_TOKEN is not set. Export ACCESS_TOKEN in your shell or provide it via GitHub Actions secrets." >&2
  exit 1
fi

# Mask the token when running in GitHub Actions
if [[ "${GITHUB_ACTIONS:-}" == "true" ]]; then
  echo "::add-mask::$TOKEN"
fi

echo "ACCESS_TOKEN found."
