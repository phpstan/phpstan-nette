<?php

namespace PHPStan\Type\Nette\Data\FormContainerOverriddenUntrustedValues;

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
	public function getUntrustedValues($returnType = null, ?array $controls = null)
	{
		return parent::getUntrustedValues($returnType, $controls);
	}

}

function (FormWithOverriddenValues $form): void {
	assertType('array<string, string>', $form->getUntrustedValues('array'));
	assertType(CustomValues::class, $form->getUntrustedValues());
};
