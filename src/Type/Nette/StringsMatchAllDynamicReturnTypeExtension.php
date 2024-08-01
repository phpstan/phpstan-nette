<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\Type;

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

		$flagsArg = $args[2] ?? null;
		$flagsType = null;
		if ($flagsArg !== null) {
			$flagsType = $scope->getType($flagsArg->value);
		}

		return $this->regexArrayShapeMatcher->matchAllExpr($patternArg->value, $flagsType, TrinaryLogic::createYes(), $scope);
	}

}
