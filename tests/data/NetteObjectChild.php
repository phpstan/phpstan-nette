<?php declare(strict_types = 1);
namespace PHPStan\Tests;

class NetteObjectChild extends \Nette\Object
{

	/** @var callable[] */
	public $onPublicEvent = [];

	/** @var callable[] */
	protected $onProtectedEvent = [];

	public static function getStaticProperty(): string
	{
		return 'static';
	}

	public function getPublicProperty(): string
	{
		return 'public';
	}

	protected function getProtectedProperty(): string
	{
		return 'protected';
	}

}
