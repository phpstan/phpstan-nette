<?php declare(strict_types=1);

namespace PHPStan\Reflection\NetteObject;

use Nette\Object;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ClassReflectionExtension;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;

class NetteObjectClassReflectionExtension implements ClassReflectionExtension
{

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		if (!$classReflection->isSubclassOf(Object::class)) {
			return false;
		}

		if (substr($propertyName, 0, 2) === 'on' && strlen($propertyName) > 2) {
			return false; // prevent infinite loop from hasMethod
		}

		// todo setter taky?

		$getterMethodName = sprintf('get%s', ucfirst($propertyName));

		return $classReflection->hasMethod($getterMethodName) && $classReflection->getMethod($getterMethodName)->isPublic();
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		return new NetteObjectPropertyReflection($classReflection);
	}

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		if (!$classReflection->isSubclassOf(Object::class)) {
			return false;
		}

		if (substr($methodName, 0, 2) !== 'on' || strlen($methodName) <= 2) {
			return false;
		}

		return $classReflection->hasProperty($methodName) && $classReflection->getProperty($methodName)->isPublic();
	}

	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
	{
		return new NetteObjectEventListenerMethodReflection($classReflection);
	}
}
