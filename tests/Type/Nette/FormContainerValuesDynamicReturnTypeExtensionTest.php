<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Composer\InstalledVersions;
use OutOfBoundsException;
use PHPStan\Testing\TypeInferenceTestCase;
use function class_exists;
use function version_compare;

final class FormContainerValuesDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	public static function dataFileAsserts(): iterable
	{
		yield from self::gatherAssertTypes(__DIR__ . '/data/FormContainerOverriddenValues.php');

		try {
			$formsVersion = class_exists(InstalledVersions::class)
				? InstalledVersions::getVersion('nette/forms')
				: null;
		} catch (OutOfBoundsException $e) {
			$formsVersion = null;
		}

		// nette/forms 3.2.9+ describes the return type in PHPDoc
		if ($formsVersion !== null && version_compare($formsVersion, '3.2.9', '>=')) {
			yield from self::gatherAssertTypes(__DIR__ . '/data/FormContainerPhpDocValues.php');
			return;
		}

		yield from self::gatherAssertTypes(__DIR__ . '/data/FormContainerModel.php');
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
