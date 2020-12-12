# Nette Framework class reflection extension for PHPStan

[![Build](https://github.com/phpstan/phpstan-nette/workflows/Build/badge.svg)](https://github.com/phpstan/phpstan-nette/actions)
[![Latest Stable Version](https://poser.pugx.org/phpstan/phpstan-nette/v/stable)](https://packagist.org/packages/phpstan/phpstan-nette)
[![License](https://poser.pugx.org/phpstan/phpstan-nette/license)](https://packagist.org/packages/phpstan/phpstan-nette)

* [PHPStan](https://phpstan.org/)
* [Nette Framework](https://nette.org/)

This extension provides following features:

* `Nette\ComponentModel\Container::getComponent()` knows type of the component because it reads the return type on `createComponent*` (this works best in presenters and controls)
* `Nette\DI\Container::getByType` and `createInstance` return type based on first parameter (`Foo::class`).
* `Nette\Forms\Container::getValues` return type based on `$asArray` parameter.
* `Nette\ComponentModel\Component::lookup` return type based on `$throw` parameter.
* `Nette\Application\UI\Component::getPresenter` return type based on `$throw` parameter.
* Dynamic methods of [Nette\Utils\Html](https://doc.nette.org/en/2.4/html-elements)
* Magic [Nette\Object and Nette\SmartObject](https://doc.nette.org/en/2.4/php-language-enhancements) properties
* Event listeners through the `on*` properties
* Defines early terminating method calls for Presenter methods to prevent `Undefined variable` errors

It also contains these framework-specific rules (can be enabled separately):

* Do not extend Nette\Object, use Nette\SmartObject trait instead
* Rethrow exceptions that are always meant to be rethrown (like `AbortException`)


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
