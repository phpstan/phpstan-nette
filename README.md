# Nette Framework class reflection extension for PHPStan

[![Build Status](https://travis-ci.org/phpstan/phpstan-nette.svg)](https://travis-ci.org/phpstan/phpstan-nette)
[![Latest Stable Version](https://poser.pugx.org/phpstan/phpstan-nette/v/stable)](https://packagist.org/packages/phpstan/phpstan-nette)
[![License](https://poser.pugx.org/phpstan/phpstan-nette/license)](https://packagist.org/packages/phpstan/phpstan-nette)

* [PHPStan](https://github.com/phpstan/phpstan)
* [Nette Framework](https://nette.org/)

This extension provides following features:

* Dynamic methods of [Nette\Utils\Html](https://doc.nette.org/en/2.4/html-elements)
* Magic [Nette\Object and Nette\SmartObject](https://doc.nette.org/en/2.4/php-language-enhancements) properties
* Event listeners through the `on*` properties

## Usage

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require phpstan/phpstan-nette
```

And include extension.neon in your project's PHPStan config:

```
includes:
	- vendor/phpstan/phpstan-nette/extension.neon
```
