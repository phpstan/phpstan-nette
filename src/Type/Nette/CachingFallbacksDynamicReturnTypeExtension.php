<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;

final class CachingFallbacksDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/** @var array<string, int> */
	private $fallbackMethods = [
		'load' => 1,
		'call' => 0,
		'wrap' => 0,
	];

	public function getClass(): string
	{
		return 'Nette\Caching\Cache';
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return array_key_exists($methodReflection->getName(), $this->fallbackMethods);
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$fallbackParameterIndex = $this->fallbackMethods[$methodReflection->getName()];

		if ($fallbackParameterIndex >= count($methodCall->args)) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		$fallbackParameterType = $scope->getType($methodCall->args[$fallbackParameterIndex]->value);
		if (!$fallbackParameterType->isCallable()->yes()) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		return ParametersAcceptorSelector::selectFromArgs($scope, $methodCall->args, $fallbackParameterType->getCallableParametersAcceptors($scope))->getReturnType();
	}

}
