<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\Strings;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\NullType;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use function array_key_exists;
use const PREG_OFFSET_CAPTURE;
use const PREG_UNMATCHED_AS_NULL;

class StringsMatchDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{

	private RegexArrayShapeMatcher $regexArrayShapeMatcher;

	public function __construct(RegexArrayShapeMatcher $regexArrayShapeMatcher)
	{
		$this->regexArrayShapeMatcher = $regexArrayShapeMatcher;
	}

	public function getClass(): string
	{
		return Strings::class;
	}

	public function isStaticMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'match';
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
	{
		$args = $methodCall->getArgs();
		$patternArg = $args[1] ?? null;

		if ($patternArg === null) {
			return null;
		}

		$arrayShape = $this->regexArrayShapeMatcher->matchExpr(
			$patternArg->value,
			$this->resolveFlagsType($args, $scope),
			TrinaryLogic::createYes(),
			$scope,
		);

		if ($arrayShape === null) {
			return null;
		}

		return TypeCombinator::union($arrayShape, new NullType());
	}

	/**
	 * @param array<Arg> $args
	 */
	private function resolveFlagsType(array $args, Scope $scope): ConstantIntegerType
	{
		if (!array_key_exists(2, $args)) {
			return new ConstantIntegerType(0);
		}

		$captureOffsetType = $scope->getType($args[2]->value);

		if ($captureOffsetType instanceof ConstantIntegerType) {
			return $captureOffsetType;
		}

		$unmatchedAsNullType = array_key_exists(4, $args) ? $scope->getType($args[4]->value) : new ConstantBooleanType(false);

		$captureOffset = $captureOffsetType->isTrue()->yes();
		$unmatchedAsNull = $unmatchedAsNullType->isTrue()->yes();

		return new ConstantIntegerType(
			($captureOffset ? PREG_OFFSET_CAPTURE : 0) | ($unmatchedAsNull ? PREG_UNMATCHED_AS_NULL : 0),
		);
	}

}
