# CLAUDE.md

This file provides guidance to Claude Code when working with this repository.

## Project Overview

**phpstan/phpstan-nette** is a PHPStan extension that provides static analysis support for the [Nette Framework](https://nette.org/). It teaches PHPStan to understand Nette-specific patterns such as dynamic method return types, magic properties, component model conventions, and framework-specific coding rules.

The extension is distributed via Composer as `phpstan/phpstan-nette` and is of type `phpstan-extension`.

## PHP Version Support

This repository supports **PHP 7.4+** (not just PHP 8.1+ like phpstan-src). All code must be compatible with PHP 7.4, 8.0, 8.1, 8.2, 8.3, and 8.4. Do not use language features unavailable in PHP 7.4.

## Nette Version Support

The extension supports multiple versions of Nette libraries:
- `nette/application` ^3.0 (conflicts with <2.3.0)
- `nette/di` ^3.0 (conflicts with <2.3.0)
- `nette/forms` ^3.0 (conflicts with <2.3.0)
- `nette/utils` ^2.3.0 || ^3.0.0 || ^4.0 (conflicts with <2.3.0)

CI runs tests with both `--prefer-lowest` and highest dependency versions to ensure compatibility across all supported Nette versions.

## Repository Structure

```
├── extension.neon       # Main extension config (type extensions, reflection, stubs)
├── rules.neon           # Optional rules config (framework-specific rules)
├── src/
│   ├── Type/Nette/      # Dynamic return type extensions (12 files)
│   ├── Reflection/Nette/ # Class reflection extensions (6 files)
│   ├── Rule/Nette/      # PHPStan rules (4 files)
│   └── Stubs/Nette/     # Stub file loader (1 file)
├── stubs/               # PHPStan stub files for Nette classes (26 files)
├── tests/
│   ├── Type/Nette/      # Type extension tests + data/
│   ├── Reflection/Nette/ # Reflection extension tests
│   ├── Rule/Nette/      # Rule tests + data/
│   └── bootstrap.php    # Test bootstrap (autoloader only)
├── phpstan.neon         # PHPStan config for analysing this project itself
├── phpunit.xml          # PHPUnit configuration
├── Makefile             # Build commands
└── composer.json        # Package definition
```

## Key Concepts

### Extension Types

1. **Dynamic Return Type Extensions** (`src/Type/Nette/`): Teach PHPStan the return types of Nette methods that depend on arguments or context (e.g., `Container::getComponent()` return type based on `createComponent*` methods, `ServiceLocator::getByType()` based on class string argument).

2. **Class Reflection Extensions** (`src/Reflection/Nette/`): Make PHPStan understand magic properties and methods on `Nette\Utils\Html` and `Nette\SmartObject`/`Nette\Object` classes.

3. **Rules** (`src/Rule/Nette/`): Framework-specific static analysis rules:
   - `DoNotExtendNetteObjectRule` — Forbids extending deprecated `Nette\Object`
   - `RethrowExceptionRule` — Ensures exceptions like `AbortException` are rethrown
   - `RegularExpressionPatternRule` — Validates regex patterns in Nette Strings methods
   - `PresenterInjectedPropertiesExtension` — Read/write extension for `@inject` properties

4. **Stub Files** (`stubs/`): Provide more precise type information for Nette classes than the original source code. Referenced in `extension.neon` under `parameters.stubFiles`.

### Configuration Files

- **`extension.neon`**: Loaded automatically by phpstan/extension-installer. Registers all type extensions, reflection extensions, stub files, early terminating methods, and universal object crates.
- **`rules.neon`**: Optional, must be explicitly included. Registers framework-specific rules and defines which Nette methods throw exceptions.

## Common Commands

```bash
# Run all checks (lint, coding standard, tests, PHPStan analysis)
make check

# Run tests only
make tests

# Run PHPStan analysis on this project at level 8
make phpstan

# Run PHP parallel lint
make lint

# Run coding standard checks (requires build-cs setup)
make cs-install   # First time: clone and install build-cs
make cs           # Check coding standard
make cs-fix       # Auto-fix coding standard violations
```

## Testing Patterns

Tests use PHPStan's built-in testing infrastructure. There are three patterns:

### Type Inference Tests
- Extend `PHPStan\Testing\TypeInferenceTestCase`
- Data files in `tests/Type/Nette/data/` contain PHP code with `assertType()` calls
- Test methods yield from `$this->gatherAssertTypes(__DIR__ . '/data/file.php')`
- Additional PHPStan config loaded via `getAdditionalConfigFiles()`

### Reflection Extension Tests
- Extend `PHPStan\Testing\PHPStanTestCase`
- Create reflection provider with `$this->createReflectionProvider()`
- Test `hasMethod()`, `getMethod()`, `hasProperty()`, `getProperty()` on class reflections
- Use `@dataProvider` for parameterized tests

### Rule Tests
- Extend `PHPStan\Testing\RuleTestCase<RuleClass>`
- Implement `getRule()` to return the rule instance
- Call `$this->analyse([files], [[message, line], ...])` to verify expected errors
- Test data files in `tests/Rule/Nette/data/`

## CI Pipeline

GitHub Actions workflow (`.github/workflows/build.yml`) on the `2.0.x` branch runs:
1. **Lint** — PHP parallel lint on PHP 7.4–8.4
2. **Coding Standard** — phpcs via phpstan/build-cs (2.x branch)
3. **Tests** — PHPUnit on PHP 7.4–8.4 with lowest and highest dependencies
4. **Static Analysis** — PHPStan level 8 on PHP 7.4–8.4 with lowest and highest dependencies

## Development Guidelines

- The main development branch is `2.0.x`.
- PSR-4 autoloading: namespace `PHPStan\` maps to `src/`.
- Tests use classmap autoloading from `tests/`.
- PHPStan analysis runs at **level 8** with strict rules, phpunit extension, and deprecation rules.
- Test data directories (`tests/*/data/*`) are excluded from PHPStan analysis.
- The coding standard is defined externally in `phpstan/build-cs` (2.x branch).
