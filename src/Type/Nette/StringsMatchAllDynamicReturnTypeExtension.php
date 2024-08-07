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
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\Type;
use function array_key_exists;
use const PREG_OFFSET_CAPTURE;
use const PREG_PATTERN_ORDER;
use const PREG_SET_ORDER;
use const PREG_UNMATCHED_AS_NULL;

class StringsMatchAllDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{

	/** @var RegexArrayShapeMatcher */
	private $regexArrayShapeMatcher;

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
		return $methodReflection->getName() === 'matchAll';
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
	{
		$args = $methodCall->getArgs();
		$patternArg = $args[1] ?? null;

		if ($patternArg === null) {
			return null;
		}

		return $this->regexArrayShapeMatcher->matchAllExpr(
			$patternArg->value,
			$this->resolveFlagsType($args, $scope),
			TrinaryLogic::createYes(),
			$scope
		);
	}

	/**
	 * @param array<Arg> $args
	 */
	private function resolveFlagsType(array $args, Scope $scope): ConstantIntegerType
	{
		if (!array_key_exists(2, $args)) {
			return new ConstantIntegerType(PREG_SET_ORDER);
		}

		$captureOffsetType = $scope->getType($args[2]->value);

		if ($captureOffsetType instanceof ConstantIntegerType) {
			$captureOffset = $captureOffsetType->getValue();
			$flags = ($captureOffset & PREG_PATTERN_ORDER) === PREG_PATTERN_ORDER ? $captureOffset : ($captureOffset | PREG_SET_ORDER);

			return new ConstantIntegerType($flags);
		}

		$unmatchedAsNullType = array_key_exists(4, $args) ? $scope->getType($args[4]->value) : new ConstantBooleanType(false);
		$patternOrderType = array_key_exists(5, $args) ? $scope->getType($args[5]->value) : new ConstantBooleanType(false);

		$captureOffset = $captureOffsetType->isTrue()->yes();
		$unmatchedAsNull = $unmatchedAsNullType->isTrue()->yes();
		$patternOrder = $patternOrderType->isTrue()->yes();

		return new ConstantIntegerType(
			($captureOffset ? PREG_OFFSET_CAPTURE : 0) | ($unmatchedAsNull ? PREG_UNMATCHED_AS_NULL : 0) | ($patternOrder ? PREG_PATTERN_ORDER : 0)
		);
	}

}
