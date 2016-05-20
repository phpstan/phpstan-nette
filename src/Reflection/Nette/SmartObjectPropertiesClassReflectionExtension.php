<?php declare(strict_types=1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;

class SmartObjectPropertiesClassReflectionExtension implements PropertiesClassReflectionExtension
{

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		$traitNames = $this->getTraitNames($classReflection->getNativeReflection());
		if (!in_array(\Nette\SmartObject::class, $traitNames, true)) {
			return false;
		}

		$property = \Nette\Utils\ObjectMixin::getMagicProperty($classReflection->getName(), $propertyName);
		if ($property === null) {
			return false;
		}

		$getterMethod = $this->getMethodByProperty($classReflection, $propertyName);
		if ($getterMethod === null) {
			return false;
		}

		return $getterMethod->isPublic();
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

}
