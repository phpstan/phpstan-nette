<?php declare(strict_types = 1);

namespace PHPStan;

use Nette\SmartObject;

class SmartObjectChild
{

	use SmartObject;

	/** @var callable[] */
	public $onPublicEvent = [];

	/** @var callable[] */
	protected $onProtectedEvent = [];

}
