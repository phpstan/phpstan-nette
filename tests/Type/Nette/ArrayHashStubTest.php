<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Iterator;
use Nette\Utils\ArrayHash;
use PHPStan\Testing\TypeInferenceTestCase;
use ReflectionMethod;
use ReflectionNamedType;

final class ArrayHashStubTest extends TypeInferenceTestCase
{

	public static function dataFileAsserts(): iterable
	{
		yield from self::gatherAssertTypes(__DIR__ . '/data/array-hash.php');

		// nette/utils 4.0+ declares getIterator(): \Iterator, older versions \RecursiveArrayIterator
		$iteratorReturnType = (new ReflectionMethod(ArrayHash::class, 'getIterator'))->getReturnType();
		if (!$iteratorReturnType instanceof ReflectionNamedType || $iteratorReturnType->getName() !== Iterator::class) {
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
