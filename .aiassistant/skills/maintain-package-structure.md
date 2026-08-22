---
name: maintain-package-structure
description: Maintain this PHP monorepo's package boundaries when adding a package or changing package Composer, namespace, autoload, README, or license structure.
---

# Maintain Package Structure

Read [`docs/agents/package-structure.md`](../../docs/agents/package-structure.md) before making a package-structure change. It is the source of truth for the monorepo's integration model and package invariants.

## Workflow

Determine whether the request changes a package boundary, package manifest, namespace mapping, or package assets. Apply the relevant requirements from the project reference, including root Composer integration when a package is added or renamed.

For a new package, `bin/create-package` is available as a starting point. Confirm authorization before any step that creates a remote repository or pushes changes. Inspect the resulting files and complete every package invariant from the project reference.

Finish by validating Composer metadata and running the relevant root quality checks. Report any existing invariant violations discovered outside the requested change separately; do not expand the requested change to correct them without authorization.
