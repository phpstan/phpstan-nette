<?php

namespace PHPStan\Type\Nette\Data\ComponentModel;

use Nette\Application\UI\Form;
use Nette\Forms\Container;
use function PHPStan\Testing\assertType;

class SomeForm31 extends Form {
}

$someForm = new SomeForm31();

assertType('array<int|string, Nette\ComponentModel\IComponent>', $someForm->getComponents(false));
assertType('array<int|string, Nette\Forms\Container>', $someForm->getComponents(false, Container::class));
assertType('Iterator<int|string, Nette\ComponentModel\IComponent>', $someForm->getComponents(true));
assertType('Iterator<int|string, Nette\Forms\Container>', $someForm->getComponents(true, Container::class));
