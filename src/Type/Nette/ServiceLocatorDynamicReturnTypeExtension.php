<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use function count;
use function in_array;

class ServiceLocatorDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\DI\Container';
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return in_array($methodReflection->getName(), [
			'getByType',
			'createInstance',
			'getService',
			'createService',
		], true);
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		$mixedType = new MixedType();
		if (in_array($methodReflection->getName(), [
			'getService',
			'createService',
		], true)) {
			return $mixedType;
		}
		if (count($methodCall->getArgs()) === 0) {
			return $mixedType;
		}
		$argType = $scope->getType($methodCall->getArgs()[0]->value);
		if (!$argType instanceof ConstantStringType) {
			return $mixedType;
		}

		$type = new ObjectType($argType->getValue());
		if (
			$methodReflection->getName() === 'getByType'
			&& count($methodCall->getArgs()) >= 2
		) {
			$throwType = $scope->getType($methodCall->getArgs()[1]->value);
			if (
				!$throwType instanceof ConstantBooleanType
				|| !$throwType->getValue()
			) {
				$type = TypeCombinator::addNull($type);
			}
		}

		return $type;
	}

}
