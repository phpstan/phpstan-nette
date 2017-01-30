<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\BrokerAwareClassReflectionExtension;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

class ComponentModelDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension, BrokerAwareClassReflectionExtension
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	public function setBroker(\PHPStan\Broker\Broker $broker)
	{
		$this->broker = $broker;
	}

	public static function getClass(): string
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
		if ($calledOnType->getClass() === null || !$this->broker->hasClass($calledOnType->getClass())) {
			return $mixedType;
		}

		$args = $methodCall->args;
		if (count($args) !== 1) {
			return $mixedType;
		}

		$arg = $args[0]->value;
		if (!$arg instanceof \PhpParser\Node\Scalar\String_) {
			return $mixedType;
		}

		$componentName = $arg->value;

		$class = $this->broker->getClass($calledOnType->getClass());
		$methodName = sprintf('createComponent%s', ucfirst($componentName));
		if (!$class->hasMethod($methodName)) {
			return $mixedType;
		}

		return $class->getMethod($methodName)->getReturnType();
	}

}
