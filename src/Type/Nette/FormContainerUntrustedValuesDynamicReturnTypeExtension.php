<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use function count;

class FormContainerUntrustedValuesDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\Forms\Container';
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		if ($methodReflection->getName() !== 'getUntrustedValues' && $methodReflection->getName() !== 'getUnsafeValues') {
			return false;
		}

		// nette/forms 3.2.9+ and methods overriding it describe the return type in PHPDoc
		if (!$methodReflection instanceof ExtendedMethodReflection) {
			return true;
		}

		$resolvedPhpDoc = $methodReflection->getResolvedPhpDoc();

		return $resolvedPhpDoc === null || $resolvedPhpDoc->getReturnTag() === null;
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type
	{
		if (count($methodCall->getArgs()) === 0) {
			return new ObjectType('Nette\Utils\ArrayHash');
		}

		$arg = $methodCall->getArgs()[0]->value;
		$scopedType = $scope->getType($arg);
		if ($scopedType->isNull()->yes()) {
			return new ObjectType('Nette\Utils\ArrayHash');
		}

		if ($scopedType->isClassString()->yes()) {
			return $scopedType->getClassStringObjectType();
		}

		if (count($scopedType->getConstantStrings()) === 1 && $scopedType->getConstantStrings()[0]->getValue() === 'array') {
			return new ArrayType(new StringType(), new MixedType());
		}

		return null;
	}

}
