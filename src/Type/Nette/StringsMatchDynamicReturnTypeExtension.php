<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\NullType;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class StringsMatchDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
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
		return $methodReflection->getName() === 'match';
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
	{
		$args = $methodCall->getArgs();
		$patternArg = $args[1] ?? null;
		if ($patternArg === null) {
			return null;
		}

		$patternType = $scope->getType($patternArg->value);
		$flagsArg = $args[2] ?? null;
		$flagsType = null;
		if ($flagsArg !== null) {
			$flagsType = $scope->getType($flagsArg->value);
		}

		$arrayShape = $this->regexArrayShapeMatcher->matchType($patternType, $flagsType, TrinaryLogic::createYes());
		if ($arrayShape === null) {
			return null;
		}

		return TypeCombinator::union($arrayShape, new NullType());
	}

}
