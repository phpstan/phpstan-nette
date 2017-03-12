<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;

class HtmlClassReflectionExtensionTest extends \PHPUnit_Framework_TestCase
{

	/** @var \PHPStan\Reflection\Nette\HtmlClassReflectionExtension */
	private $extension;

	protected function setUp()
	{
		$this->extension = new HtmlClassReflectionExtension();
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasMethod(): array
	{
		return [
			[
				\Nette\Utils\Html::class,
				true,
			],
			[
				\stdClass::class,
				false,
			],
		];
	}

	/**
	 * @dataProvider dataHasMethod
	 * @param string $className
	 * @param bool $result
	 */
	public function testHasMethod(string $className, bool $result)
	{
		$classReflection = $this->createMock(ClassReflection::class);
		$classReflection->method('getName')->will($this->returnValue($className));
		$this->assertSame($result, $this->extension->hasMethod($classReflection, 'href'));
	}

	public function testGetMethod()
	{
		$classReflection = $this->createMock(ClassReflection::class);
		$classReflection->method('getName')->will($this->returnValue(\Nette\Utils\Html::class));
		$methodReflection = $this->extension->getMethod($classReflection, 'href');
		$this->assertSame('href', $methodReflection->getName());
		$this->assertSame($classReflection, $methodReflection->getDeclaringClass());
		$this->assertFalse($methodReflection->isStatic());
		$this->assertEmpty($methodReflection->getParameters());
		$this->assertTrue($methodReflection->isVariadic());
		$this->assertFalse($methodReflection->isPrivate());
		$this->assertTrue($methodReflection->isPublic());
		$this->assertInstanceOf(ObjectType::class, $methodReflection->getReturnType());
		$this->assertSame(\Nette\Utils\Html::class, $methodReflection->getReturnType()->getClass());
		$this->assertFalse($methodReflection->getReturnType()->isNullable());
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasProperty(): array
	{
		return [
			[
				\Nette\Utils\Html::class,
				true,
			],
			[
				\stdClass::class,
				false,
			],
		];
	}

	/**
	 * @dataProvider dataHasProperty
	 * @param string $className
	 * @param bool $result
	 */
	public function testHasProperty(string $className, bool $result)
	{
		$classReflection = $this->createMock(ClassReflection::class);
		$classReflection->method('getName')->will($this->returnValue($className));
		$this->assertSame($result, $this->extension->hasProperty($classReflection, 'href'));
	}

	public function testGetProperty()
	{
		$classReflection = $this->createMock(ClassReflection::class);
		$classReflection->method('getName')->will($this->returnValue(\Nette\Utils\Html::class));
		$propertyReflection = $this->extension->getProperty($classReflection, 'href');
		$this->assertSame($classReflection, $propertyReflection->getDeclaringClass());
		$this->assertFalse($propertyReflection->isStatic());
		$this->assertFalse($propertyReflection->isPrivate());
		$this->assertTrue($propertyReflection->isPublic());
		$this->assertInstanceOf(MixedType::class, $propertyReflection->getType());
		$this->assertTrue($propertyReflection->getType()->isNullable());
	}

}
