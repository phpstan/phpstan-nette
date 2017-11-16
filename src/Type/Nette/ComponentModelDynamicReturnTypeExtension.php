<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

class ComponentModelDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return \Nette\ComponentModel\Container::class;
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
		$args = $methodCall->args;
		if (count($args) !== 1) {
			return $mixedType;
		}

		$arg = $args[0]->value;
		if (!$arg instanceof \PhpParser\Node\Scalar\String_) {
			return $mixedType;
		}

		$componentName = $arg->value;

		$methodName = sprintf('createComponent%s', ucfirst($componentName));
		if (!$calledOnType->hasMethod($methodName)) {
			return $mixedType;
		}

		return $calledOnType->getMethod($methodName, $scope)->getReturnType();
	}

}
