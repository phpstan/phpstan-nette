<?php

namespace PHPStan\Type\Nette\Data\ComponentModel;

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
assertType('PHPStan\Type\Nette\Data\ComponentModel\SomeControl', $someControl->getComponent('some'));
assertType('Nette\ComponentModel\IComponent|null', $someControl->getComponent('unknown'));

$anotherControl = new AnotherControl();
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $anotherControl->getComponent('another'));
assertType('PHPStan\Type\Nette\Data\ComponentModel\SomeControl', $anotherControl->getComponent('some'));
assertType('Nette\ComponentModel\IComponent|null', $anotherControl->getComponent('unknown'));

$overrideCreateControl = new OverrideCreateControl();
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $overrideCreateControl->getComponent('some'));
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $overrideCreateControl->getComponent('unknown'));
