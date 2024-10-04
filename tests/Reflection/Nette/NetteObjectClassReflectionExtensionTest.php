<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ReflectionProvider;
use PHPStan\SmartObjectChild;
use PHPStan\Testing\PHPStanTestCase;
use function class_exists;
use function sprintf;

class NetteObjectClassReflectionExtensionTest extends PHPStanTestCase
{

	private ReflectionProvider $reflectionProvider;

	private NetteObjectClassReflectionExtension $extension;

	protected function setUp(): void
	{
		$this->reflectionProvider = $this->createReflectionProvider();
		$this->extension = new NetteObjectClassReflectionExtension();
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasMethod(): array
	{
		$data = [];
		$data[] = [
			SmartObjectChild::class,
			'onPublicEvent',
			true,
		];
		$data[] = [
			SmartObjectChild::class,
			'onProtectedEvent',
			false,
		];
		$data[] = [
			'PHPStan\NetteObjectChild',
			'onPublicEvent',
			true,
		];
		$data[] = [
			'PHPStan\NetteObjectChild',
			'onProtectedEvent',
			false,
		];
		return $data;
	}

	/**
	 * @dataProvider dataHasMethod
	 */
	public function testHasMethod(string $className, string $method, bool $result): void
	{
		if (!class_exists('Nette\LegacyObject')) {
			self::markTestSkipped(sprintf('%s does not exist.', $className));
		}
		$classReflection = $this->reflectionProvider->getClass($className);
		self::assertSame($result, $this->extension->hasMethod($classReflection, $method));
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasProperty(): array
	{
		$data = [];
		$data[] = [
			SmartObjectChild::class,
			'foo',
			false,
		];
		$data[] = [
			'PHPStan\NetteObjectChild',
			'staticProperty',
			false,
		];
		$data[] = [
			'PHPStan\NetteObjectChild',
			'publicProperty',
			true,
		];
		$data[] = [
			'PHPStan\NetteObjectChild',
			'protectedProperty',
			false,
		];

		return $data;
	}

	/**
	 * @dataProvider dataHasProperty
	 */
	public function testHasProperty(string $className, string $property, bool $result): void
	{
		if (!class_exists('Nette\LegacyObject')) {
			self::markTestSkipped(sprintf('%s does not exist.', $className));
		}
		$classReflection = $this->reflectionProvider->getClass($className);
		self::assertSame($result, $this->extension->hasProperty($classReflection, $property));
	}

}
