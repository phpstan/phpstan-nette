<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PHPStan\Testing\TypeInferenceTestCase;

final class NetteUtilsJsonDecodeDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public function dataAsserts(): iterable
	{
		yield from $this->gatherAssertTypes(__DIR__ . '/data/json_decode.php');
		yield from $this->gatherAssertTypes(__DIR__ . '/data/json_decode_force_array.php');

		yield from $this->gatherAssertTypes(__DIR__ . '/data/json_decode_unknown_type.php');
		yield from $this->gatherAssertTypes(__DIR__ . '/data/json_decode_force_array_unknown_type.php');
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
		return [__DIR__ . '/config/nette_json_decode_extension.neon'];
	}

}
