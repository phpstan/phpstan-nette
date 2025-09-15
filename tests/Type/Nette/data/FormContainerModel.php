<?php

namespace PHPStan\Type\Nette\Data\FormContainerModel;

use Nette\Forms\Form;
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

class FormContainerModel
{
	public function test()
	{
		$form = new Form();
		$form->addText('name');
		$form->addText('value');

		$dto = $form->getValues(Dto::class);
		$array = $form->getValues('array');

		assertType(Dto::class, $dto);
		assertType('array<string, mixed>', $array);
	}
}
