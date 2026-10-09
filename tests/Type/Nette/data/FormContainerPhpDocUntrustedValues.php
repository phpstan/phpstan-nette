<?php

namespace PHPStan\Type\Nette\Data\FormContainerPhpDocUntrustedValues;

use Nette\Forms\Form;
use function PHPStan\Testing\assertType;

class Dto
{

	public string $name;

}

function (Form $form, Dto $dto): void {
	assertType(Dto::class, $form->getUntrustedValues(Dto::class));
	assertType(Dto::class, $form->getUntrustedValues($dto));
};
