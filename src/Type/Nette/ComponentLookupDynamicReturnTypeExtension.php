<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use function count;

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
		$defaultReturnType = ParametersAcceptorSelector::selectFromArgs(
			$scope,
			$methodCall->getArgs(),
			$methodReflection->getVariants(),
		)->getReturnType();
		if (count($methodCall->getArgs()) < 2) {
			return $defaultReturnType;
		}

		$paramNeedExpr = $methodCall->getArgs()[1]->value;
		$paramNeedType = $scope->getType($paramNeedExpr);

		if ($paramNeedType->isTrue()->yes()) {
			return TypeCombinator::removeNull($defaultReturnType);
		}
		if ($paramNeedType->isFalse()->yes()) {
			return TypeCombinator::addNull($defaultReturnType);
		}

		return $defaultReturnType;
	}

}
