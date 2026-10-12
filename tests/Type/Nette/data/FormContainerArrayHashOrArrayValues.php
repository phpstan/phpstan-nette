<?php

namespace PHPStan\Type\Nette\Data\FormContainerArrayHashOrArrayValues;

use Nette\Forms\Form;
use Nette\Utils\ArrayHash;
use function PHPStan\Testing\assertType;

class CustomValues extends ArrayHash
{

}

/**
 * nette/forms 2.x describes the return type of getValues() as ArrayHash|array
 */
class FormWithArrayHashOrArrayValues extends Form
{

	/**
	 * @return ArrayHash|array
	 */
	public function getValues($returnType = null, ?array $controls = null)
	{
		return parent::getValues($returnType, $controls);
	}

}

class FormWithNarrowerValues extends Form
{

	/**
	 * @return CustomValues|array<string, string>
	 */
	public function getValues($returnType = null, ?array $controls = null)
	{
		return parent::getValues($returnType, $controls);
	}

}

function (FormWithArrayHashOrArrayValues $form): void {
	assertType('array<string, mixed>', $form->getValues(true));
	assertType('array<string, mixed>', $form->getValues('array'));
	assertType(ArrayHash::class, $form->getValues());
	assertType(ArrayHash::class, $form->getValues(null));
};

function (FormWithNarrowerValues $form): void {
	assertType('array<string, string>|' . CustomValues::class, $form->getValues(true));
	assertType('array<string, string>|' . CustomValues::class, $form->getValues());
};
