<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use function in_array;

class HtmlClassReflectionExtension implements MethodsClassReflectionExtension, PropertiesClassReflectionExtension
{

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		// The static factories declared in the stub, which would otherwise be taken for attribute setters
		return $classReflection->is('Nette\Utils\Html') && !in_array($methodName, ['text', 'html'], true);
	}

	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
	{
		return new HtmlMethodReflection($methodName, $classReflection);
	}

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		return $classReflection->is('Nette\Utils\Html');
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		return new HtmlPropertyReflection($classReflection);
	}

}
