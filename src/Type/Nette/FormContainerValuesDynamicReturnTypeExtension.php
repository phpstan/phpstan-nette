<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use function count;

final class FormContainerValuesDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\Forms\Container';
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'getValues';
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		$args = $methodCall->getArgs();

		if (count($args) === 0) {
			return new ObjectType('Nette\Utils\ArrayHash');
		}

		$arg = $args[0]->value;
		$scopedType = $scope->getType($arg);

		if ($scopedType->isTrue()->yes()) {
			return new ArrayType(new StringType(), new MixedType());
		}

		$constantStrings = $scopedType->getConstantStrings();

		if (count($constantStrings) === 0) {
			return new ObjectType('Nette\Utils\ArrayHash');
		}

		$constantString = $constantStrings[0];

		$value = $constantString->getValue();

		if ($scopedType->isClassString()->yes()) {
			return $scopedType->getClassStringObjectType();
		}

		if ($value === 'array') {
			return new ArrayType(new StringType(), new MixedType());
		}

		return new ObjectType('Nette\Utils\ArrayHash');
	}

}
