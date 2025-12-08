<?php declare(strict_types = 1);

use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;

use function PHPStan\Testing\assertType;

/** @var Multiplier<Form> $multiplier */
$multiplier = new Multiplier(function (string $name): ?Form {
	if($name === 'foo') {
		return new Form();
	} else {
		return null;
	}
});

assertType('Nette\Application\UI\Multiplier<Nette\Application\UI\Form>', $multiplier);

$form = $multiplier->createComponent('foo');

assertType(Form::class . '|null', $form);

$notSupportedForm = $multiplier->createComponent('notSupported');

assertType(Form::class . '|null', $notSupportedForm);
