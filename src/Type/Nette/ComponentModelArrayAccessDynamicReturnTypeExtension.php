<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use function count;
use function sprintf;
use function ucfirst;

class ComponentModelArrayAccessDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return 'Nette\Application\UI\Component';
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'offsetGet';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$calledOnType = $scope->getType($methodCall->var);
		$defaultType = $calledOnType->getMethod('createComponent', $scope)->getVariants()[0]->getReturnType();
		$defaultType = TypeCombinator::remove($defaultType, new NullType());
		if ($defaultType->isSuperTypeOf(new ObjectType('Nette\ComponentModel\IComponent'))->yes()) {
			$defaultType = new MixedType(false, new NullType());
		}
		$args = $methodCall->getArgs();
		if (count($args) < 1) {
			return $defaultType;
		}

		$argType = $scope->getType($args[0]->value);
		if (count($argType->getConstantStrings()) === 0) {
			return $defaultType;
		}

		$types = [];
		foreach ($argType->getConstantStrings() as $constantString) {
			$componentName = $constantString->getValue();

			$methodName = sprintf('createComponent%s', ucfirst($componentName));
			if (!$calledOnType->hasMethod($methodName)->yes()) {
				return $defaultType;
			}

			$method = $calledOnType->getMethod($methodName, $scope);

			$types[] = ParametersAcceptorSelector::selectFromArgs(
				$scope,
				[new Arg(new String_($componentName))],
				$method->getVariants(),
			)->getReturnType();
		}

		return TypeCombinator::union(...$types);
	}

}
