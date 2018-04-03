<?php declare(strict_types = 1);

namespace PHPStan;

class SmartObjectChild
{

	use \Nette\SmartObject;

	/** @var callable[] */
	public $onPublicEvent = [];

	/** @var callable[] */
	protected $onProtectedEvent = [];

}
