# Nette Framework extension for PHPStan

[![Build](https://github.com/phpstan/phpstan-nette/workflows/Build/badge.svg)](https://github.com/phpstan/phpstan-nette/actions)
[![Latest Stable Version](https://poser.pugx.org/phpstan/phpstan-nette/v/stable)](https://packagist.org/packages/phpstan/phpstan-nette)
[![License](https://poser.pugx.org/phpstan/phpstan-nette/license)](https://packagist.org/packages/phpstan/phpstan-nette)

* [PHPStan](https://phpstan.org/)
* [Nette Framework](https://nette.org/)

This extension provides following features:

* `Nette\ComponentModel\Container::getComponent()` knows type of the component because it reads the return type on `createComponent*` (this works best in presenters and controls)
* Array access on components (`$component['name']`) resolves types via `createComponent*` methods
* `Nette\DI\Container::getByType` and `createInstance` return type based on first parameter (`Foo::class`).
* `Nette\Forms\Container::getValues` return type based on `$asArray` parameter.
* `Nette\Forms\Container::getUntrustedValues` return type based on mapping class parameter.
* `Nette\Forms\Controls\BaseControl` fluent methods return `static` instead of `BaseControl`.
* `Nette\ComponentModel\Component::lookup` return type based on `$throw` parameter.
* `Nette\Application\UI\Component::getPresenter` return type based on `$throw` parameter.
* `Nette\Application\UI\Presenter::getSession` returns `Session` or `SessionSection` based on arguments.
* Dynamic methods of [Nette\Utils\Html](https://doc.nette.org/en/2.4/html-elements)
* Magic [Nette\Object and Nette\SmartObject](https://doc.nette.org/en/2.4/php-language-enhancements) properties
* Event listeners through the `on*` properties
* Presenter `@inject` properties are treated as initialized.
* Defines early terminating method calls for Presenter methods to prevent `Undefined variable` errors
* Understand the exact array shape coming from `Nette\Utils\Strings::match()` and `Nette\Utils\Strings::matchAll()` based on pattern
* `Nette\Utils\Strings::replace()` callback closure parameter type inferred from regex pattern

It also contains these framework-specific rules (can be enabled separately):

* Do not extend Nette\Object, use Nette\SmartObject trait instead
* Rethrow exceptions that are always meant to be rethrown (like `AbortException`)
* Validate regex patterns passed to `Nette\Utils\Strings` methods


## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev phpstan/phpstan-nette
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include extension.neon in your project's PHPStan config:

```
includes:
    - vendor/phpstan/phpstan-nette/extension.neon
```

To perform framework-specific checks, include also this file:

```
    - vendor/phpstan/phpstan-nette/rules.neon
```

</details>
