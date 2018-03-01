<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

class NetteObjectClassReflectionExtensionTest extends \PHPStan\Testing\TestCase
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	/** @var \PHPStan\Reflection\Nette\NetteObjectClassReflectionExtension */
	private $extension;

	protected function setUp()
	{
		$this->broker = $this->createBroker();
		$this->extension = new NetteObjectClassReflectionExtension();
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasMethod(): array
	{
		$data = [];
		$data[] = [
			\PHPStan\Tests\SmartObjectChild::class,
			'onPublicEvent',
			true,
		];
		$data[] = [
			\PHPStan\Tests\SmartObjectChild::class,
			'onProtectedEvent',
			false,
		];
		if (PHP_VERSION_ID < 70200) { // PHP 7.2 is incompatible with Nette\Object.
			$data[] = [
				'PHPStan\Tests\NetteObjectChild',
				'onPublicEvent',
				true,
			];
			$data[] = [
				'PHPStan\Tests\NetteObjectChild',
				'onProtectedEvent',
				false,
			];
		}
		return $data;
	}

	/**
	 * @dataProvider dataHasMethod
	 * @param string $className
	 * @param string $method
	 * @param bool $result
	 */
	public function testHasMethod(string $className, string $method, bool $result)
	{
		$classReflection = $this->broker->getClass($className);
		self::assertSame($result, $this->extension->hasMethod($classReflection, $method));
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasProperty(): array
	{
		$data = [];
		$data[] = [
			\PHPStan\Tests\SmartObjectChild::class,
			'foo',
			false,
		];
		if (PHP_VERSION_ID < 70200) { // PHP 7.2 is incompatible with Nette\Object.
			$data[] = [
				'PHPStan\Tests\NetteObjectChild',
				'staticProperty',
				false,
			];
			$data[] = [
				'PHPStan\Tests\NetteObjectChild',
				'publicProperty',
				true,
			];
			$data[] = [
				'PHPStan\Tests\NetteObjectChild',
				'protectedProperty',
				false,
			];
		}
		return $data;
	}

	/**
	 * @dataProvider dataHasProperty
	 * @param string $className
	 * @param string $property
	 * @param bool $result
	 */
	public function testHasProperty(string $className, string $property, bool $result)
	{
		$classReflection = $this->broker->getClass($className);
		self::assertSame($result, $this->extension->hasProperty($classReflection, $property));
	}

}
