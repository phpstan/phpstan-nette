<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class ComponentLookupDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return \Nette\ComponentModel\Component::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'lookup';
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		if (count($methodCall->args) < 2) {
			return $methodReflection->getReturnType();
		}

		$paramNeedExpr = $methodCall->args[1]->value;
		$paramNeedType = $scope->getType($paramNeedExpr);

		if ($paramNeedType instanceof ConstantBooleanType) {
			if ($paramNeedType->getValue()) {
				return TypeCombinator::removeNull($methodReflection->getReturnType());
			}

			return TypeCombinator::addNull($methodReflection->getReturnType());
		}

		return $methodReflection->getReturnType();
	}

}
