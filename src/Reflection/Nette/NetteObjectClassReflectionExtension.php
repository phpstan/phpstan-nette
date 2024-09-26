<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Nette;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionClass;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionEnum;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use function array_merge;
use function array_unique;
use function array_values;
use function in_array;
use function sprintf;
use function strlen;
use function substr;
use function ucfirst;

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

	private function getMethodByProperty(ClassReflection $classReflection, string $propertyName): ?MethodReflection
	{
		$getterMethodName = sprintf('get%s', ucfirst($propertyName));
		if (!$classReflection->hasNativeMethod($getterMethodName)) {
			return null;
		}

		return $classReflection->getNativeMethod($getterMethodName);
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		/** @var MethodReflection $getterMethod */
		$getterMethod = $this->getMethodByProperty($classReflection, $propertyName);
		return new NetteObjectPropertyReflection($classReflection, $getterMethod->getVariants()[0]->getReturnType());
	}

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		$traitNames = $this->getTraitNames($classReflection->getNativeReflection());
		if (!in_array('Nette\SmartObject', $traitNames, true) && !$this->inheritsFromNetteObject($classReflection->getNativeReflection())) {
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
	 * @param ReflectionClass|ReflectionEnum $class
	 * @return string[]
	 */
	private function getTraitNames($class): array
	{
		$traitNames = $class->getTraitNames();
		while ($class->getParentClass() !== false) {
			$traitNames = array_values(array_unique(array_merge($traitNames, $class->getParentClass()->getTraitNames())));
			$class = $class->getParentClass();
		}

		return $traitNames;
	}

	/**
	 * @param ReflectionClass|ReflectionEnum $class
	 */
	private function inheritsFromNetteObject($class): bool
	{
		$class = $class->getParentClass();
		while ($class !== false) {
			if (in_array($class->getName(), [ // @phpstan-ignore-line
				'Nette\Object',
				'Nette\LegacyObject',
			], true)) {
				return true;
			}
			$class = $class->getParentClass();
		}

		return false;
	}

}
