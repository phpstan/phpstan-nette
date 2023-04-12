<?php

namespace PHPStan\Type\Nette\Data\ComponentModelArrayAccess;

use Nette\Application\UI\Control;
use function PHPStan\Testing\assertType;

class SomeControl extends Control {

	public function createComponentSome(): self {
		return new SomeControl();
	}

}

class AnotherControl extends Control {

	public function createComponentAnother(): AnotherControl {
		return new AnotherControl();
	}

	public function createComponentSome(): SomeControl {
		return new SomeControl();
	}

}

class OverrideCreateControl extends Control {

	public function createComponent(string $name): AnotherControl {
		return new AnotherControl();
	}

}

$someControl = new SomeControl();
assertType('PHPStan\Type\Nette\Data\ComponentModelArrayAccess\SomeControl', $someControl['some']);
assertType('mixed~null', $someControl['unknown']);

$anotherControl = new AnotherControl();
assertType('PHPStan\Type\Nette\Data\ComponentModelArrayAccess\AnotherControl', $anotherControl['another']);
assertType('PHPStan\Type\Nette\Data\ComponentModelArrayAccess\SomeControl', $anotherControl['some']);
assertType('mixed~null', $anotherControl['unknown']);

$overrideCreateControl = new OverrideCreateControl();
assertType('PHPStan\Type\Nette\Data\ComponentModelArrayAccess\AnotherControl', $overrideCreateControl['some']);
assertType('PHPStan\Type\Nette\Data\ComponentModelArrayAccess\AnotherControl', $overrideCreateControl['unknown']);
