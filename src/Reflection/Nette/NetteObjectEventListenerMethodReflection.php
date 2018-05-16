<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\VoidType;

class NetteObjectEventListenerMethodReflection implements MethodReflection
{

	/** @var string */
	private $name;

	/** @var \PHPStan\Reflection\ClassReflection */
	private $declaringClass;

	public function __construct(string $name, ClassReflection $declaringClass)
	{
		$this->name = $name;
		$this->declaringClass = $declaringClass;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDeclaringClass(): ClassReflection
	{
		return $this->declaringClass;
	}

	public function getPrototype(): ClassMemberReflection
	{
		return $this;
	}

	public function isStatic(): bool
	{
		return false;
	}

	/**
	 * @return \PHPStan\Reflection\ParametersAcceptor[]
	 */
	public function getVariants(): array
	{
		return [
			new FunctionVariant(
				[],
				true,
				new VoidType()
			),
		];
	}

	public function isPrivate(): bool
	{
		return false;
	}

	public function isPublic(): bool
	{
		return true;
	}

}
