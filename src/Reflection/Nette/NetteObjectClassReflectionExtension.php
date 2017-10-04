<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;

class NetteObjectClassReflectionExtension implements MethodsClassReflectionExtension, PropertiesClassReflectionExtension
{

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		if (!$this->inheritsFromNetteObject($classReflection->getNativeReflection())) {
			return false;
		}

		$getterMethod = $this->getMethodByProperty($classReflection, $propertyName);
		if ($getterMethod === null) {
			return false;
		}
		if ($getterMethod->isStatic()) {
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
		if (!$classReflection->hasNativeMethod($getterMethodName)) {
			return null;
		}

		return $classReflection->getNativeMethod($getterMethodName);
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		/** @var \PHPStan\Reflection\MethodReflection $getterMethod */
		$getterMethod = $this->getMethodByProperty($classReflection, $propertyName);
		return new NetteObjectPropertyReflection($classReflection, $getterMethod->getReturnType());
	}

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		$traitNames = $this->getTraitNames($classReflection->getNativeReflection());
		if (!in_array(\Nette\SmartObject::class, $traitNames, true) && !$this->inheritsFromNetteObject($classReflection->getNativeReflection())) {
			return false;
		}

		if (substr($methodName, 0, 2) !== 'on' || strlen($methodName) <= 2) {
			return false;
		}

		return $classReflection->hasNativeProperty($methodName) && $classReflection->getNativeProperty($methodName)->isPublic();
	}

	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
	{
		return new NetteObjectEventListenerMethodReflection($methodName, $classReflection);
	}

	/**
	 * @param \ReflectionClass $class
	 * @return string[]
	 */
	private function getTraitNames(\ReflectionClass $class): array
	{
		$traitNames = $class->getTraitNames();
		while ($class->getParentClass() !== false) {
			$traitNames = array_values(array_unique(array_merge($traitNames, $class->getParentClass()->getTraitNames())));
			$class = $class->getParentClass();
		}

		return $traitNames;
	}

	private function inheritsFromNetteObject(\ReflectionClass $class): bool
	{
		while (($class = $class->getParentClass()) !== false) {
			if ($class->getName() === 'Nette\Object') {
				return true;
			}
		}

		return false;
	}

}
