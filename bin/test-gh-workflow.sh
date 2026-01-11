#!/usr/bin/env bash

set -euo pipefail

if [[ -z "$ACCESS_TOKEN" ]]; then
  echo "Missing github access token" >&2
  exit 1
fi

export ORG=php-addition-repository
export DRY_RUN=1

bin/gh-workflow/discover_packages.sh "$ORG" packages > /tmp/packages.json
cat /tmp/packages.json | jq .

# Test with branch
BEFORE="$(git rev-parse HEAD~1)"
bin/gh-workflow/plan_packages.sh branch "$BEFORE" "$(git rev-parse HEAD)" /tmp/packages.json > /tmp/selected.json
cat /tmp/selected.json | jq .
bin/gh-workflow/split_and_push.sh /tmp/selected.json branch - "$(git rev-parse HEAD)"

# Test with tag
TAG="v0.1.0-test"
bin/gh-workflow/plan_packages.sh tag - "$(git rev-parse HEAD)" /tmp/packages.json > /tmp/selected.json
cat /tmp/selected.json | jq .
bin/gh-workflow/split_and_push.sh /tmp/selected.json tag "$TAG" "$(git rev-parse HEAD)"

# Cleanup
rm -rf /tmp/packages.json
rm -rf /tmp/selected.json
