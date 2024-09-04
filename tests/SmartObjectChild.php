<?php declare(strict_types = 1);

namespace PHPStan;

use Nette\SmartObject;

class SmartObjectChild
{

	use SmartObject;

	/** @var callable[] */
	public array $onPublicEvent = [];

	/** @var callable[] */
	protected array $onProtectedEvent = [];

}
