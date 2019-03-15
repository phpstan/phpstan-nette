<?php declare(strict_types=1);

namespace PHPStan\Reflection\Nette;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Native\NativeParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\CallableType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VoidType;

final class FormEventsReflectionExtension implements PropertiesClassReflectionExtension
{

	/**
	 * @var array<string, PropertyReflection>
	 */
	private $eventTypes;

	private function initialize(ClassReflection $classReflection): bool
	{
		$this->eventTypes = [
			'onSuccess' => self::createReflection(new CallableType([new NativeParameterReflection('form', false, new ObjectType(Form::class), PassedByReference::createNo(), false), new NativeParameterReflection('values', true, TypeCombinator::union(new ObjectType(ArrayHash::class), new ArrayType(new StringType(), new MixedType())), PassedByReference::createNo(), false)], new VoidType(), false), $classReflection),
			'onError' => self::createReflection(new CallableType([new NativeParameterReflection('form', false, new ObjectType(Form::class), PassedByReference::createNo(), false)], new VoidType(), false), $classReflection),
			'onSubmit' => self::createReflection(new CallableType([new NativeParameterReflection('form', false, new ObjectType(Form::class), PassedByReference::createNo(), false), new NativeParameterReflection('values', true, TypeCombinator::union(new ObjectType(ArrayHash::class), new ArrayType(new StringType(), new MixedType())), PassedByReference::createNo(), false)], new VoidType(), false), $classReflection),
			'onRender' => self::createReflection(new CallableType([new NativeParameterReflection('form', false, new ObjectType(Form::class), PassedByReference::createNo(), false)], new VoidType(), false), $classReflection),
			'onAttached' => self::createReflection(new CallableType([new NativeParameterReflection('form', false, new ObjectType(Form::class), PassedByReference::createNo(), false)], new VoidType(), false), $classReflection),
		];

		return true;
	}

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		return ($classReflection->isSubclassOf(Form::class) || $classReflection->getName() === Form::class)
			&& $this->initialize($classReflection)
			&& array_key_exists($propertyName, $this->eventTypes);
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		return $this->eventTypes[$propertyName];
	}

	private static function createReflection(Type $propertyType, ClassReflection $classReflection): PropertyReflection
	{
		return new class($propertyType, $classReflection) implements PropertyReflection
		{
			/** @var Type */
			private $propertyType;

			/** @var ClassReflection */
			private $classReflection;

			public function __construct(Type $propertyType, ClassReflection $classReflection)
			{
				$this->propertyType = $propertyType;
				$this->classReflection = $classReflection;
			}

			public function getDeclaringClass(): ClassReflection
			{
				return $this->classReflection;
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

			public function getType(): Type
			{
				return $this->propertyType;
			}

			public function isReadable(): bool
			{
				return true;
			}

			public function isWritable(): bool
			{
				return true;
			}
		};
	}

}
