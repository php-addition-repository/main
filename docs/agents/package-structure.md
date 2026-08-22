# Package Structure

This repository is a PHP 8.3 monorepo. Each directory in `packages/` is an independently publishable Composer library; the root project composes them into one development environment.

## Root integration

The root `composer.json` is the authoritative integration point:

- `replace` declares every local `par/<package>` library.
- `autoload.psr-4` exposes every package production namespace from `packages/<package>/src/`.
- `autoload-dev.psr-4` exposes every package test namespace from `packages/<package>/tests/`.

Use PHP 8.3 features freely. Composer is flattened for local development, so a local package may use another local package without declaring it as an internal requirement. Preserve each package's own `composer.json` so it can later be split and published independently.

## Package invariants

Every package contains:

- `composer.json` with a `license` field;
- `README.md` based on `.templates/README.md` with its placeholders replaced;
- `LICENSE`, copied from the root license;
- `src/` and `tests/` directories, each mapped by PSR-4 in its package manifest.

`bin/create-package` is the existing helper for a new package. Its output is a starting point: verify every invariant and the root integration before considering the change complete. The helper can create and push a remote repository, so obtain the user's authorization before running the portions that do so.

## Validation

After a structural change, validate that the root and package Composer files are valid and that the root test suite still passes. Run the relevant Composer quality checks when the change affects source code or configuration they inspect.
