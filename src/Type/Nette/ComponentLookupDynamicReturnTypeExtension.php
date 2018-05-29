<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class ComponentLookupDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\ComponentModel\Component';
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'lookup';
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		$defaultReturnType = ParametersAcceptorSelector::selectSingle(
			$methodReflection->getVariants()
		)->getReturnType();
		if (count($methodCall->args) < 2) {
			return $defaultReturnType;
		}

		$paramNeedExpr = $methodCall->args[1]->value;
		$paramNeedType = $scope->getType($paramNeedExpr);

		if ($paramNeedType instanceof ConstantBooleanType) {
			if ($paramNeedType->getValue()) {
				return TypeCombinator::removeNull($defaultReturnType);
			}

			return TypeCombinator::addNull($defaultReturnType);
		}

		return $defaultReturnType;
	}

}
