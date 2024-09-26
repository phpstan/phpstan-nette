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

final class ComponentGetPresenterDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\Application\UI\Component';
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'getPresenter';
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		$methodDefinition = ParametersAcceptorSelector::selectFromArgs(
			$scope,
			$methodCall->getArgs(),
			$methodReflection->getVariants(),
		);
		$defaultReturnType = $methodDefinition->getReturnType();
		$firstParameterExists = count($methodDefinition->getParameters()) > 0;

		if (count($methodCall->getArgs()) < 1) {
			if (!$firstParameterExists) {
				return TypeCombinator::removeNull($defaultReturnType);
			}

			return $defaultReturnType;
		}

		$paramNeedExpr = $methodCall->getArgs()[0]->value;
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
