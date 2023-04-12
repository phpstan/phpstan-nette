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

$bool = rand(0, 1) ? true : false;

$someControl = new SomeControl();
assertType('PHPStan\Type\Nette\Data\ComponentModel\SomeControl', $someControl->getComponent('some'));
assertType('mixed~null', $someControl->getComponent('unknown'));
assertType('mixed', $someControl->getComponent('unknown', false));
assertType('mixed~null', $someControl->getComponent('unknown', true));
assertType('mixed', $someControl->getComponent('unknown', $bool));

$anotherControl = new AnotherControl();
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $anotherControl->getComponent('another'));
assertType('PHPStan\Type\Nette\Data\ComponentModel\SomeControl', $anotherControl->getComponent('some'));
assertType('mixed~null', $anotherControl->getComponent('unknown'));
assertType('mixed', $anotherControl->getComponent('unknown', false));
assertType('mixed~null', $anotherControl->getComponent('unknown', true));
assertType('mixed', $anotherControl->getComponent('unknown', $bool));

$overrideCreateControl = new OverrideCreateControl();
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $overrideCreateControl->getComponent('some'));
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $overrideCreateControl->getComponent('unknown'));
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $overrideCreateControl->getComponent('unknown', false));
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $overrideCreateControl->getComponent('unknown', true));
assertType('PHPStan\Type\Nette\Data\ComponentModel\AnotherControl', $overrideCreateControl->getComponent('unknown', $bool));
