---
apply: always
---

### Project Structure

- This is a PHP monorepo.
- The `packages/` directory contains multiple independent Composer libraries.
- Each subdirectory in `packages/` is a standalone package with its own `composer.json`.
- Root `composer.json` uses path repositories to include packages from the `packages/` directory.

### Tech Stack & Standards

- Language: PHP 8.3
- Dependency Management: Composer
- Coding Standard: PSR-12 (or specify your preferred standard)
- Autoloading: PSR-4

### Development Guidelines

- Always assume PHP 8.3 features are available (e.g., typed constants, readonly classes, etc.).
- When adding new functionality, check if it belongs in an existing package under `packages/` or requires a new one.
- Dependencies between packages should be managed via `composer.json` using the `par/` namespace.

# Package structure

- Each package must have a README file that uses `.templates/README.md` as template.
- Each package must contain a copy of the root LICENSE file.
- Each package `composer.json` must contain a `license`.
