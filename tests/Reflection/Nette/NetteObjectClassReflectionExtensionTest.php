<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;

class NetteObjectClassReflectionExtensionTest extends \PHPUnit\Framework\TestCase
{

	/** @var \PHPStan\Reflection\Nette\NetteObjectClassReflectionExtension */
	private $extension;

	protected function setUp()
	{
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
		$classReflection = $this->mockClassReflection(new \ReflectionClass($className));
		$this->assertSame($result, $this->extension->hasMethod($classReflection, $method));
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
		$classReflection = $this->mockClassReflection(new \ReflectionClass($className));
		$this->assertSame($result, $this->extension->hasProperty($classReflection, $property));
	}

	private function mockClassReflection(\ReflectionClass $class): ClassReflection
	{
		$classReflection = $this->createMock(ClassReflection::class);
		$classReflection->method('getNativeReflection')->will($this->returnValue($class));
		$classReflection->method('hasNativeProperty')->will(
			$this->returnCallback(
				function (string $property) use ($class): bool {
					return $class->hasProperty($property);
				}
			)
		);
		$classReflection->method('getNativeProperty')->will(
			$this->returnCallback(
				function (string $property) use ($class): PhpPropertyReflection {
					return $this->mockPropertyReflection($class->getProperty($property));
				}
			)
		);
		$classReflection->method('hasNativeMethod')->will(
			$this->returnCallback(
				function (string $method) use ($class): bool {
					return $class->hasMethod($method);
				}
			)
		);
		$classReflection->method('getNativeMethod')->will(
			$this->returnCallback(
				function (string $method) use ($class): PhpMethodReflection {
					return $this->mockMethodReflection($class->getMethod($method));
				}
			)
		);

		return $classReflection;
	}

	private function mockMethodReflection(\ReflectionMethod $method): PhpMethodReflection
	{
		$methodReflection = $this->createMock(PhpMethodReflection::class);
		$methodReflection->method('isPublic')->will($this->returnValue($method->isPublic()));
		$methodReflection->method('isStatic')->will($this->returnValue($method->isStatic()));
		return $methodReflection;
	}

	private function mockPropertyReflection(\ReflectionProperty $property): PhpPropertyReflection
	{
		$propertyReflection = $this->createMock(PhpPropertyReflection::class);
		$propertyReflection->method('isPublic')->will($this->returnValue($property->isPublic()));
		return $propertyReflection;
	}

}
