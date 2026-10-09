<?php

namespace PHPStan\Type\Nette\Data\FormContainerOverriddenValues;

use Nette\Forms\Form;
use Nette\Utils\ArrayHash;
use function PHPStan\Testing\assertType;

class CustomValues extends ArrayHash
{

}

class FormWithOverriddenValues extends Form
{

	/**
	 * @return ($returnType is 'array' ? array<string, string> : CustomValues)
	 */
	public function getValues($returnType = null, ?array $controls = null)
	{
		return parent::getValues($returnType, $controls);
	}

}

function (FormWithOverriddenValues $form): void {
	assertType('array<string, string>', $form->getValues('array'));
	assertType(CustomValues::class, $form->getValues());
};
