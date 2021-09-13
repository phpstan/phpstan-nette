<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\VerbosityLevel;

class HtmlClassReflectionExtensionTest extends \PHPStan\Testing\PHPStanTestCase
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	/** @var \PHPStan\Reflection\Nette\HtmlClassReflectionExtension */
	private $extension;

	protected function setUp(): void
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
	public function testHasMethod(string $className, bool $result): void
	{
		$classReflection = $this->broker->getClass($className);
		self::assertSame($result, $this->extension->hasMethod($classReflection, 'href'));
	}

	public function testGetMethod(): void
	{
		$classReflection = $this->broker->getClass(\Nette\Utils\Html::class);
		$methodReflection = $this->extension->getMethod($classReflection, 'href');
		$parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());
		self::assertSame('href', $methodReflection->getName());
		self::assertSame($classReflection, $methodReflection->getDeclaringClass());
		self::assertFalse($methodReflection->isStatic());
		self::assertEmpty($parametersAcceptor->getParameters());
		self::assertTrue($parametersAcceptor->isVariadic());
		self::assertFalse($methodReflection->isPrivate());
		self::assertTrue($methodReflection->isPublic());
		self::assertSame(\Nette\Utils\Html::class, $parametersAcceptor->getReturnType()->describe(VerbosityLevel::value()));
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
	public function testHasProperty(string $className, bool $result): void
	{
		$classReflection = $this->broker->getClass($className);
		self::assertSame($result, $this->extension->hasProperty($classReflection, 'href'));
	}

	public function testGetProperty(): void
	{
		$classReflection = $this->broker->getClass(\Nette\Utils\Html::class);
		$propertyReflection = $this->extension->getProperty($classReflection, 'href');
		self::assertSame($classReflection, $propertyReflection->getDeclaringClass());
		self::assertFalse($propertyReflection->isStatic());
		self::assertFalse($propertyReflection->isPrivate());
		self::assertTrue($propertyReflection->isPublic());
		self::assertSame('mixed', $propertyReflection->getReadableType()->describe(VerbosityLevel::value()));
	}

}
