<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use Nette\Utils\Html;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\VerbosityLevel;
use stdClass;

class HtmlClassReflectionExtensionTest extends PHPStanTestCase
{

	private ReflectionProvider $reflectionProvider;

	private HtmlClassReflectionExtension $extension;

	protected function setUp(): void
	{
		$this->reflectionProvider = $this->createReflectionProvider();
		$this->extension = new HtmlClassReflectionExtension();
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasMethod(): array
	{
		return [
			[
				Html::class,
				true,
			],
			[
				stdClass::class,
				false,
			],
		];
	}

	/**
	 * @dataProvider dataHasMethod
	 */
	public function testHasMethod(string $className, bool $result): void
	{
		$classReflection = $this->reflectionProvider->getClass($className);
		self::assertSame($result, $this->extension->hasMethod($classReflection, 'href'));
	}

	public function testGetMethod(): void
	{
		$classReflection = $this->reflectionProvider->getClass(Html::class);
		$methodReflection = $this->extension->getMethod($classReflection, 'href');
		$parametersAcceptor = $methodReflection->getVariants()[0];
		self::assertSame('href', $methodReflection->getName());
		self::assertSame($classReflection, $methodReflection->getDeclaringClass());
		self::assertFalse($methodReflection->isStatic());
		self::assertEmpty($parametersAcceptor->getParameters());
		self::assertTrue($parametersAcceptor->isVariadic());
		self::assertFalse($methodReflection->isPrivate());
		self::assertTrue($methodReflection->isPublic());
		self::assertSame(Html::class, $parametersAcceptor->getReturnType()->describe(VerbosityLevel::value()));
	}

	/**
	 * @return mixed[]
	 */
	public function dataHasProperty(): array
	{
		return [
			[
				Html::class,
				true,
			],
			[
				stdClass::class,
				false,
			],
		];
	}

	/**
	 * @dataProvider dataHasProperty
	 */
	public function testHasProperty(string $className, bool $result): void
	{
		$classReflection = $this->reflectionProvider->getClass($className);
		self::assertSame($result, $this->extension->hasProperty($classReflection, 'href'));
	}

	public function testGetProperty(): void
	{
		$classReflection = $this->reflectionProvider->getClass(Html::class);
		$propertyReflection = $this->extension->getProperty($classReflection, 'href');
		self::assertSame($classReflection, $propertyReflection->getDeclaringClass());
		self::assertFalse($propertyReflection->isStatic());
		self::assertFalse($propertyReflection->isPrivate());
		self::assertTrue($propertyReflection->isPublic());
		self::assertSame('mixed', $propertyReflection->getReadableType()->describe(VerbosityLevel::value()));
	}

}
