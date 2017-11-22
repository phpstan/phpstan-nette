<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\FalseBooleanType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\TrueBooleanType;
use PHPStan\Type\Type;

final class FormContainerValuesDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return \Nette\Forms\Container::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'getValues';
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		if (count($methodCall->args) === 0) {
			return new ObjectType(\Nette\Utils\ArrayHash::class);
		}

		$arg = $methodCall->args[0]->value;
		$scopedType = $scope->getType($arg);

		if ($scopedType instanceof FalseBooleanType) {
			return new ObjectType(\Nette\Utils\ArrayHash::class);
		}

		if ($scopedType instanceof TrueBooleanType) {
			return new ArrayType(new StringType(), new MixedType());
		}

		return $methodReflection->getReturnType();
	}

}
