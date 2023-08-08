<?php declare(strict_types = 1);

namespace PHPStan\Nette;

use Nette\DI\Container;
use PHPStan\ShouldNotHappenException;
use function is_file;
use function is_readable;
use function sprintf;

class ContainerResolver
{

	/** @var string|null */
	private $containerLoader;

	/** @var Container|false|null */
	private $container;

	public function __construct(?string $containerLoader)
	{
		$this->containerLoader = $containerLoader;
	}

	public function getContainer(): ?Container
	{
		if ($this->container === false) {
			return null;
		}

		if ($this->container !== null) {
			return $this->container;
		}

		if ($this->containerLoader === null) {
			$this->container = false;

			return null;
		}

		$this->container = $this->loadContainer($this->containerLoader);

		return $this->container;
	}


	private function loadContainer(string $containerLoader): ?Container
	{
		if (!is_file($containerLoader)) {
			throw new ShouldNotHappenException(sprintf(
				'Nette container could not be loaded: file "%s" does not exist',
				$containerLoader
			));
		}

		if (!is_readable($containerLoader)) {
			throw new ShouldNotHappenException(sprintf(
				'Nette container could not be loaded: file "%s" is not readable',
				$containerLoader
			));
		}

		return require $containerLoader;
	}

}
