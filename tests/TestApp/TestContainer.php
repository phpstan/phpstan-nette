<?php declare(strict_types = 1);

namespace PHPStan\TestApp;

use Nette\Application\IPresenterFactory;
use Nette\Application\PresenterFactory;
use Nette\DI\Container;

class TestContainer extends Container
{

	/** @var array @phpstan-ignore-next-line */
	protected $wiring = [
		'Nette\Application\IPresenterFactory' => [
			0 => ['presenterFactory'],
		],
	];

	protected function createServicePresenterFactory(): IPresenterFactory
	{
		$service = new PresenterFactory();
		$service->setMapping(['*' => 'PHPStan\TestApp\Presenters\*\*Presenter']);
		return $service;
	}

}
