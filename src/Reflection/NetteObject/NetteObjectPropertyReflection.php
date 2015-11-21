<?php declare(strict_types=1);

namespace PHPStan\Reflection\NetteObject;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;

class NetteObjectPropertyReflection implements PropertyReflection
{

	/** @var \PHPStan\Reflection\ClassReflection */
	private $declaringClass;

	public function __construct(ClassReflection $declaringClass)
	{
		$this->declaringClass = $declaringClass;
	}

	public function getDeclaringClass(): ClassReflection
	{
		return $this->declaringClass;
	}

	public function isStatic(): bool
	{
		return false;
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
