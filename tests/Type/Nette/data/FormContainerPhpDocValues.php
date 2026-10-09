<?php

namespace PHPStan\Type\Nette\Data\FormContainerPhpDocValues;

use Nette\Forms\Form;
use function PHPStan\Testing\assertType;

class Dto
{

	public string $name;

}

function (Form $form, Dto $dto): void {
	assertType(Dto::class, $form->getValues(Dto::class));
	assertType(Dto::class, $form->getValues($dto));
};
