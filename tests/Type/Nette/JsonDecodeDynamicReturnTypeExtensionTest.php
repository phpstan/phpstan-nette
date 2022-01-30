<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PHPStan\Testing\TypeInferenceTestCase;

final class JsonDecodeDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public function dataAsserts(): iterable
	{
		yield from $this->gatherAssertTypes(__DIR__ . '/data/json_decode.php');
		yield from $this->gatherAssertTypes(__DIR__ . '/data/json_decode_force_array.php');
	}

	/**
	 * @dataProvider dataAsserts()
	 * @param string $assertType
	 * @param string $file
	 * @param mixed ...$args
	 */
	public function testAsserts(string $assertType, string $file, ...$args): void
	{
		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	/**
	 * @return string[]
	 */
	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/config/json_decode_extension.neon'];
	}

}
