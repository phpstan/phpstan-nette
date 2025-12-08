<?php declare(strict_types = 1);

namespace PHPStan\Stubs\Nette\Application;

use Composer\InstalledVersions;
use OutOfBoundsException;
use PHPStan\PhpDoc\StubFilesExtension;
use function class_exists;
use function dirname;
use function version_compare;

class StubFilesExtensionLoader implements StubFilesExtension
{

	public function getFiles(): array
	{
		$path = dirname(dirname(dirname(dirname(__DIR__)))) . '/stubs';

		try {
			$applicationVersion = class_exists(InstalledVersions::class)
				? InstalledVersions::getVersion('nette/application')
				: null;
		} catch (OutOfBoundsException $e) {
			$applicationVersion = null;
		}

		$files = [];

		if ($applicationVersion !== null && version_compare($applicationVersion, '3.2.5', '>=')) {
			$files[] = $path . '/Application/UI/NullableMultiplier.stub';
		} else {
			$files[] = $path . '/Application/UI/Multiplier.stub';
		}

		return $files;
	}

}
