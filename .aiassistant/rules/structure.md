---
apply: always
---

### Project Structure

- This is a PHP monorepo.
- The `packages/` directory contains multiple independent Composer libraries.
- Each subdirectory in `packages/` is a standalone package with its own `composer.json`.
- Root `composer.json` uses the `replace` configuration to manage packages in the `packages/` directory.
- Autoloading for all packages is defined in the root `composer.json`.

### Tech Stack & Standards

- Language: PHP 8.3
- Dependency Management: Composer (Monolithic approach using `replace`)
- Coding Standard: PER-3.0 (using `php-cs-fixer`)
- Autoloading: PSR-4 (configured at root)

### Development Guidelines

- Always assume PHP 8.3 features are available (e.g., typed constants, readonly classes, etc.).
- When adding a new package to `packages/`, update the `replace` and `autoload` sections in the root `composer.json`.
- Internal dependencies between packages do not need to be explicitly required in `composer.json` due to the flattened
  structure, but sub-package `composer.json` files should be kept for potential future splits.

# Package structure

- Each package must have a README file that uses `.templates/README.md` as template.
- Each package must contain a copy of the root LICENSE file.
- Each package `composer.json` must contain a `license`.
