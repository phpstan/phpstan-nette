<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;

class FormsBaseControlDynamicReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{

	public function getClass(): string
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
		$returnType = $methodReflection->getReturnType();
		$referencedClasses = $returnType->getReferencedClasses();
		if (
			count($referencedClasses) === 1
			&& $referencedClasses[0] === \Nette\Forms\Controls\BaseControl::class
		) {
			return $scope->getType($methodCall->var);
		}

		return $methodReflection->getReturnType();
	}

}
