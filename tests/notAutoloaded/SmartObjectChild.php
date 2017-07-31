<?php declare(strict_types = 1);
namespace PHPStan\Tests;

class SmartObjectChild
{

	use \Nette\SmartObject;

	/** @var callable[] */
	public $onPublicEvent = [];

	/** @var callable[] */
	protected $onProtectedEvent = [];

}
