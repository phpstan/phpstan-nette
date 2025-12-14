<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Composer\InstalledVersions;
use OutOfBoundsException;
use PHPStan\Testing\TypeInferenceTestCase;
use function class_exists;
use function version_compare;

final class FormContainerUntrustedValuesDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	public static function dataFileAsserts(): iterable
	{
		try {
			$formsVersion = class_exists(InstalledVersions::class)
				? InstalledVersions::getVersion('nette/forms')
				: null;
		} catch (OutOfBoundsException $e) {
			$formsVersion = null;
		}

		if ($formsVersion === null || version_compare($formsVersion, '3.1.10', '<')) {
			return;
		}

		yield from self::gatherAssertTypes(__DIR__ . '/data/FormContainerUntrustedValues.php');
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
