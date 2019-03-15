<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Type\VerbosityLevel;

final class FormEventsReflectionExtensionTest extends \PHPStan\Testing\TestCase
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	/** @var \PHPStan\Reflection\Nette\FormEventsReflectionExtension */
	private $extension;

	protected function setUp(): void
	{
		$this->broker = $this->createBroker();
		$this->extension = new FormEventsReflectionExtension();
	}

	public function testGetProperty(): void
	{
		$classReflection = $this->broker->getClass(\Nette\Application\UI\Form::class);
		self::assertTrue($this->extension->hasProperty($classReflection, 'onSuccess'));
		$propertyReflection = $this->extension->getProperty($classReflection, 'onSuccess');
		self::assertSame($classReflection, $propertyReflection->getDeclaringClass());
		self::assertFalse($propertyReflection->isStatic());
		self::assertFalse($propertyReflection->isPrivate());
		self::assertTrue($propertyReflection->isPublic());
		self::assertSame(
			sprintf('callable(%s, array<string, mixed>|%s): void', \Nette\Application\UI\Form::class, \Nette\Utils\ArrayHash::class),
			$propertyReflection->getType()->describe(VerbosityLevel::value())
		);
	}

}
