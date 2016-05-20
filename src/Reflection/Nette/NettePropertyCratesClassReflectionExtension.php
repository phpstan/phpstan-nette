<?php declare(strict_types=1);

namespace PHPStan\Reflection\Nette;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;

class NettePropertyCratesClassReflectionExtension implements PropertiesClassReflectionExtension
{

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		$classes = [
			'Nette\Application\UI\ITemplate',
			'Nette\Http\SessionSection',
		];

		if (in_array($classReflection->getName(), $classes, true)) {
			return true;
		}

		foreach ($classes as $class) {
			if ($classReflection->getNativeReflection()->isSubclassOf($class)) {
				return true;
			}
		}

		return false;
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		return new NetteCrateProperty($classReflection);
	}

}
