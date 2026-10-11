<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Composer\InstalledVersions;
use OutOfBoundsException;
use PHPStan\Testing\TypeInferenceTestCase;
use function class_exists;
use function version_compare;

final class ArrayHashStubTest extends TypeInferenceTestCase
{

	public static function dataFileAsserts(): iterable
	{
		yield from self::gatherAssertTypes(__DIR__ . '/data/array-hash.php');

		try {
			$utilsVersion = class_exists(InstalledVersions::class)
				? InstalledVersions::getVersion('nette/utils')
				: null;
		} catch (OutOfBoundsException $e) {
			$utilsVersion = null;
		}

		// nette/utils 4.0+ declares getIterator(): \Iterator, older versions \RecursiveArrayIterator
		if ($utilsVersion === null || version_compare($utilsVersion, '4.0.0', '<')) {
			return;
		}

		yield from self::gatherAssertTypes(__DIR__ . '/data/array-hash-iterator.php');
	}

	/**
	 * @dataProvider dataFileAsserts
	 * @param mixed ...$args
	 */
	public function testFileAsserts(
		string $assertType,
		string $file,
		...$args
	): void
	{
		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/phpstan.neon',
		];
	}

}
