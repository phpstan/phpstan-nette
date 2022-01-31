<?php declare(strict_types = 1);

namespace PHPStan\Type\Php;

use Nette\Utils\Json;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ConstantTypeHelper;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use stdClass;

final class JsonDecodeDynamicReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
	public function isFunctionSupported(FunctionReflection $functionReflection): bool
	{
		// @todo - finds only "assertType", but not "json_decode" :/
		dump($functionReflection->getName());

		return $functionReflection->getName() === 'json_decode';
	}

	public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $funcCall, Scope $scope): Type
	{
		$args = $funcCall->getArgs();

		dump('___');
		die;

		$isForceArray = $this->isForceArray($funcCall);

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

	private function resolveConstantStringType(ConstantStringType $constantStringType, bool $isForceArray): Type
	{
		if ($isForceArray) {
			$decodedValue = Json::decode($constantStringType->getValue(), Json::FORCE_ARRAY);
		} else {
			$decodedValue = Json::decode($constantStringType->getValue());
		}

		return ConstantTypeHelper::getTypeFromValue($decodedValue);
	}
}
