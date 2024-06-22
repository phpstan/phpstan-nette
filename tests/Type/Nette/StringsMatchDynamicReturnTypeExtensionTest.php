<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PHPStan\Testing\TypeInferenceTestCase;
use const PHP_VERSION_ID;

class StringsMatchDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<string, mixed[]>
	 */
	public function dataFileAsserts(): iterable
	{
		if (PHP_VERSION_ID < 70400) {
			return;
		}

		yield from self::gatherAssertTypes(__DIR__ . '/data/strings-match.php');
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
			'phar://' . __DIR__ . '/../../../vendor/phpstan/phpstan/phpstan.phar/conf/bleedingEdge.neon',
			__DIR__ . '/phpstan.neon',
		];
	}

}
