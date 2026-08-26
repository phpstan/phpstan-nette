<?php

namespace PHPStan\Type\Nette\Data\ComponentModel;

use Nette\Application\UI\Form;
use Nette\Forms\Container;
use function PHPStan\Testing\assertType;

class SomeForm extends Form {
}

$someForm = new SomeForm();

assertType('Iterator<int|string, Nette\ComponentModel\IComponent>', $someForm->getComponents(false));
assertType('Iterator<int|string, Nette\Forms\Container>', $someForm->getComponents(false, Container::class));
assertType('Iterator<int|string, Nette\ComponentModel\IComponent>', $someForm->getComponents(true));
assertType('Iterator<int|string, Nette\Forms\Container>', $someForm->getComponents(true, Container::class));
