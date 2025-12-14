<?php

namespace PHPStan\Type\Nette\Data\FormContainerUntrustedValues;

use Nette\Forms\Form;
use Nette\Utils\ArrayHash;
use function PHPStan\Testing\assertType;

class Dto
{
	public string $name;
	public string $value;

	public function __construct(
		string $name,
		string $value
	)
	{
		$this->name = $name;
		$this->name = $value;
	}
}

class FormContainerUntrustedValues
{
	public function test()
	{
		$form = new Form();
		$form->addText('name');
		$form->addText('value');

		$dto = $form->getUntrustedValues(Dto::class);
		$array = $form->getUntrustedValues('array');

		assertType(Dto::class, $dto);
		assertType('array<string, mixed>', $array);

		assertType(ArrayHash::class, $form->getUntrustedValues());
		assertType(ArrayHash::class, $form->getUntrustedValues(null));
	}
}
