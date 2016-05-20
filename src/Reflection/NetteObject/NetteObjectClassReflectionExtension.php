<?php declare(strict_types=1);

namespace PHPStan\Reflection\NetteObject;

use Nette\Object;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;

class NetteObjectClassReflectionExtension implements MethodsClassReflectionExtension, PropertiesClassReflectionExtension
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

		$getterMethod = $this->getMethodByProperty($classReflection, $propertyName);
		if ($getterMethod === null) {
			return false;
		}

		return $getterMethod->isPublic();
	}

	/**
	 * @param \PHPStan\Reflection\ClassReflection $classReflection
	 * @param string $propertyName
	 * @return \PHPStan\Reflection\MethodReflection|null
	 */
	private function getMethodByProperty(ClassReflection $classReflection, string $propertyName)
	{
		$getterMethodName = sprintf('get%s', ucfirst($propertyName));
		if (!$classReflection->hasMethod($getterMethodName)) {
			return null;
		}

		return $classReflection->getMethod($getterMethodName);
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		$getterMethod = $this->getMethodByProperty($classReflection, $propertyName);
		return new NetteObjectPropertyReflection($classReflection, $getterMethod->getReturnType());
	}

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		$traitNames = $this->getTraitNames($classReflection->getNativeReflection());
		if (!$classReflection->isSubclassOf(Object::class) && !in_array(\Nette\SmartObject::class, $traitNames, true)) {
			return false;
		}

		if (substr($methodName, 0, 2) !== 'on' || strlen($methodName) <= 2) {
			return false;
		}

		return $classReflection->hasProperty($methodName) && $classReflection->getProperty($methodName)->isPublic();
	}

	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
	{
		return new NetteObjectEventListenerMethodReflection($methodName, $classReflection);
	}

	private function getTraitNames(\ReflectionClass $class): array
	{
		$traitNames = $class->getTraitNames();
		while ($class->getParentClass() !== false) {
			$traitNames = array_values(array_unique(array_merge($traitNames, $class->getParentClass()->getTraitNames())));
			$class = $class->getParentClass();
		}

		return $traitNames;
	}
}
