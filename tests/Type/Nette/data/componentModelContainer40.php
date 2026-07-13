<?php

namespace PHPStan\Type\Nette\Data\ComponentModel;

use Nette\Application\UI\Form;
use function PHPStan\Testing\assertType;

class SomeForm40 extends Form {
}

$someForm = new SomeForm40();

assertType('array<Nette\ComponentModel\IComponent>', $someForm->getComponents());
