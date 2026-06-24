<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Composer\InstalledVersions;
use OutOfBoundsException;
use PHPStan\Testing\TypeInferenceTestCase;
use function class_exists;
use function version_compare;

class TypeInferenceTest extends TypeInferenceTestCase
{

	public function dataFileAsserts(): iterable
	{
		try {
			$applicationVersion = class_exists(InstalledVersions::class)
				? InstalledVersions::getVersion('nette/application')
				: null;
		} catch (OutOfBoundsException $e) {
			$applicationVersion = null;
		}

		if ($applicationVersion !== null && version_compare($applicationVersion, '3.2.5', '>=')) {
			yield from self::gatherAssertTypes(__DIR__ . '/data/multiplierApplication325.php');
		} else {
			yield from self::gatherAssertTypes(__DIR__ . '/data/multiplier.php');
		}

		yield from $this->gatherAssertTypes(__DIR__ . '/data/strings-length.php');
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
			__DIR__ . '/../../../extension.neon',
		];
	}

}
