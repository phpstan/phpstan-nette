<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use function count;
use function sprintf;
use function ucfirst;

class ComponentModelDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\ComponentModel\Container';
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'getComponent';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$calledOnType = $scope->getType($methodCall->var);
		$mixedType = new MixedType();
		$args = $methodCall->getArgs();
		if (count($args) !== 1) {
			return $mixedType;
		}

		$argType = $scope->getType($args[0]->value);
		if (!$argType instanceof ConstantStringType) {
			return $mixedType;
		}

		$componentName = $argType->getValue();

		$methodName = sprintf('createComponent%s', ucfirst($componentName));
		if (!$calledOnType->hasMethod($methodName)->yes()) {
			return $mixedType;
		}

		$method = $calledOnType->getMethod($methodName, $scope);

		return ParametersAcceptorSelector::selectSingle($method->getVariants())->getReturnType();
	}

}
