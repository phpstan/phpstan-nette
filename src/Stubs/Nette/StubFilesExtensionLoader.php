<?php declare(strict_types = 1);

namespace PHPStan\Stubs\Nette;

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
		$stubsDir = dirname(dirname(dirname(__DIR__))) . '/stubs';
		$path = $stubsDir;

		$files = [];

		$componentModelVersion = self::getInstalledVersion('nette/component-model');

		if ($componentModelVersion === null) {
			return [];
		}

		if (version_compare($componentModelVersion, '3.1.0', '<')) {
			$files[] = $path . '/ComponentModel/Container.stub';
		} elseif (version_compare($componentModelVersion, '4.0.0', '<')) {
			$files[] = $path . '/ComponentModel/Container_3_1.stub';
		}

		return $files;
	}

	private static function getInstalledVersion(string $package): ?string
	{
		if (!class_exists(InstalledVersions::class)) {
			return null;
		}

		try {
			$installedVersion = InstalledVersions::getVersion($package);
		} catch (OutOfBoundsException $e) {
			return null;
		}

		return $installedVersion;
	}

}
