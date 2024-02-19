<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use Nette\DI\Attributes\Inject;
use PHPStan\Php\PhpVersion;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Rules\Properties\ReadWritePropertiesExtension;
use function strpos;

class PresenterInjectedPropertiesExtension implements ReadWritePropertiesExtension
{

	/** @var PhpVersion */
	private $phpVersion;

	public function __construct(PhpVersion $phpVersion)
	{
		$this->phpVersion = $phpVersion;
	}

	public function isAlwaysRead(PropertyReflection $property, string $propertyName): bool
	{
		return false;
	}

	public function isAlwaysWritten(PropertyReflection $property, string $propertyName): bool
	{
		return $this->isInitialized($property, $propertyName);
	}

	public function isInitialized(PropertyReflection $property, string $propertyName): bool
	{
		if (!$property->isPublic()) {
			return false;
		}

		if (strpos($property->getDocComment() ?? '', '@inject') !== false) {
			return true;
		}

		$nativeProperty = $property->getDeclaringClass()->getNativeProperty($propertyName)->getNativeReflection();
		if ($this->phpVersion->getVersionId() >= 80000 && $nativeProperty->getAttributes(Inject::class) !== []) {
			return true;
		}

		return false;
	}

}
