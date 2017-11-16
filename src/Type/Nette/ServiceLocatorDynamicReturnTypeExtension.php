<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TrueBooleanType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class ServiceLocatorDynamicReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return \Nette\DI\Container::class;
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
		if (count($methodCall->args) === 0) {
			return $mixedType;
		}
		$arg = $methodCall->args[0]->value;
		if (!($arg instanceof \PhpParser\Node\Expr\ClassConstFetch)) {
			return $mixedType;
		}

		$class = $arg->class;
		if (!($class instanceof \PhpParser\Node\Name)) {
			return $mixedType;
		}

		$class = (string) $class;

		if ($class === 'static') {
			return $mixedType;
		}

		if ($class === 'self') {
			$class = $scope->getClassReflection()->getName();
		}

		$type = new ObjectType($class);
		if (
			$methodReflection->getName() === 'getByType'
			&& count($methodCall->args) >= 2
			&& $scope->getType($methodCall->args[1]->value) instanceof TrueBooleanType
		) {
			$type = TypeCombinator::addNull($type);
		}

		return $type;
	}

}
