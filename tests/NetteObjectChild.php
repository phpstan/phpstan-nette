<?php declare(strict_types = 1);

namespace PHPStan;

use Nette\LegacyObject;

class NetteObjectChild extends LegacyObject
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
