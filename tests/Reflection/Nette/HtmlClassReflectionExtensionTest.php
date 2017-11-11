<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

class HtmlClassReflectionExtensionTest extends \PHPStan\Testing\TestCase
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	/** @var \PHPStan\Reflection\Nette\HtmlClassReflectionExtension */
	private $extension;

	protected function setUp()
	{
		$this->broker = $this->createBroker();
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
		$classReflection = $this->broker->getClass($className);
		$this->assertSame($result, $this->extension->hasMethod($classReflection, 'href'));
	}

	public function testGetMethod()
	{
		$classReflection = $this->broker->getClass(\Nette\Utils\Html::class);
		$methodReflection = $this->extension->getMethod($classReflection, 'href');
		$this->assertSame('href', $methodReflection->getName());
		$this->assertSame($classReflection, $methodReflection->getDeclaringClass());
		$this->assertFalse($methodReflection->isStatic());
		$this->assertEmpty($methodReflection->getParameters());
		$this->assertTrue($methodReflection->isVariadic());
		$this->assertFalse($methodReflection->isPrivate());
		$this->assertTrue($methodReflection->isPublic());
		$this->assertSame(\Nette\Utils\Html::class, $methodReflection->getReturnType()->describe());
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
		$classReflection = $this->broker->getClass($className);
		$this->assertSame($result, $this->extension->hasProperty($classReflection, 'href'));
	}

	public function testGetProperty()
	{
		$classReflection = $this->broker->getClass(\Nette\Utils\Html::class);
		$propertyReflection = $this->extension->getProperty($classReflection, 'href');
		$this->assertSame($classReflection, $propertyReflection->getDeclaringClass());
		$this->assertFalse($propertyReflection->isStatic());
		$this->assertFalse($propertyReflection->isPrivate());
		$this->assertTrue($propertyReflection->isPublic());
		$this->assertSame('mixed', $propertyReflection->getType()->describe());
	}

}
