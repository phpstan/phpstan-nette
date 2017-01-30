<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;

class FormsBaseControlDynamicReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{

	public static function getClass(): string
	{
		return \Nette\Forms\Controls\BaseControl::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getDeclaringClass()->getName() === \Nette\Forms\Controls\BaseControl::class;
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		if ($methodReflection->getReturnType()->getClass() !== null && $methodReflection->getReturnType()->getClass() === \Nette\Forms\Controls\BaseControl::class) {
			return $scope->getType($methodCall->var);
		}

		return $methodReflection->getReturnType();
	}

}
