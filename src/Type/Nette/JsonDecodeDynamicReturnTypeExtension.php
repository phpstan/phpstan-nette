<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\Json;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use stdClass;

final class JsonDecodeDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\Utils\Json';
	}

	public function isStaticMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'decode';
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
	{
		$args = $methodCall->getArgs();

		$isForceArray = $this->isForceArray($args);

		$firstArgValue = $args[0]->value;
		$firstValueType = $scope->getType($firstArgValue);

		if ($firstValueType instanceof ConstantStringType) {
			$resolvedType = $this->resolveConstantStringType($firstValueType, $isForceArray);
		} else {
			$resolvedType = new MixedType();
		}

		if (! $resolvedType instanceof MixedType) {
			return $resolvedType;
		}

		// fallback type
		if ($isForceArray) {
			return new UnionType([
				new ArrayType(new MixedType(), new MixedType()),
				new StringType(),
				new FloatType(),
				new IntegerType(),
				new BooleanType(),
			]);
		}

		// scalar types with stdClass
		return new UnionType([
			new ObjectType(stdClass::class),
			new StringType(),
			new FloatType(),
			new IntegerType(),
			new BooleanType(),
		]);
	}

	/**
	 * @param Arg[] $args
	 */
	private function isForceArray(array $args): bool
	{
		if (!isset($args[1])) {
			return false;
		}

		$secondArg = $args[1];

		// is second arg force array?
		if ($secondArg->value instanceof ClassConstFetch) {
			$classConstFetch = $secondArg->value;

			if ($classConstFetch->class instanceof Name) {
				if (! $classConstFetch->name instanceof \PhpParser\Node\Identifier) {
					return false;
				}

				if ($classConstFetch->class->toString() !== 'Nette\Utils\Json') {
					return false;
				}

				if ($classConstFetch->name->toString() === 'FORCE_ARRAY') {
					return true;
				}
			}
		}

		return false;
	}

	private function resolveConstantStringType(ConstantStringType $constantStringType, bool $isForceArray): Type
	{
		if ($isForceArray) {
			$decodedValue = Json::decode($constantStringType->getValue(), Json::FORCE_ARRAY);
		} else {
			$decodedValue = Json::decode($constantStringType->getValue());
		}

		if (is_bool($decodedValue)) {
			return new BooleanType();
		}

		if (is_array($decodedValue)) {
			return new ArrayType(new MixedType(), new MixedType());
		}

		if (is_object($decodedValue) && get_class($decodedValue) === stdClass::class) {
			return new ObjectType(stdClass::class);
		}

		if (is_int($decodedValue)) {
			return new IntegerType();
		}

		if (is_float($decodedValue)) {
			return new FloatType();
		}

		return new MixedType();
	}

}
