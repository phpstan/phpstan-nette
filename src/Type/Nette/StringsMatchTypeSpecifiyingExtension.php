<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\Strings;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\NullType;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use function array_key_exists;
use const PREG_OFFSET_CAPTURE;
use const PREG_UNMATCHED_AS_NULL;

class StringsMatchTypeSpecifiyingExtension implements StaticMethodTypeSpecifyingExtension
{
	private RegexArrayShapeMatcher $regexArrayShapeMatcher;

	private TypeSpecifier $typeSpecifier;

	public function __construct(RegexArrayShapeMatcher $regexArrayShapeMatcher)
	{
		$this->regexArrayShapeMatcher = $regexArrayShapeMatcher;
	}

	public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
	{
		$this->typeSpecifier = $typeSpecifier;
	}

	public function getClass(): string
	{
		return Strings::class;
	}

	public function isStaticMethodSupported(MethodReflection $staticMethodReflection, StaticCall $node, TypeSpecifierContext $context): bool
	{
		return $context->true() && $staticMethodReflection->getName() === 'match';
	}

	public function specifyTypes(MethodReflection $staticMethodReflection, StaticCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
	{
		$args = $node->getArgs();
		$subjectArg = $args[0] ?? null;
		$patternArg = $args[1] ?? null;

		$subjectTypes = new SpecifiedTypes();
		if ($patternArg === null) {
			return $subjectTypes;
		}

		if (
			$subjectArg !== null
			&& $context->true()
			&& $scope->getType($subjectArg->value)->isString()->yes()
		) {
			$subjectType = $this->regexArrayShapeMatcher->matchSubjectExpr($patternArg->value, $scope);
			if ($subjectType !== null) {
				$subjectTypes = $this->typeSpecifier->create(
					$subjectArg->value,
					$subjectType,
					$context,
					$scope,
				)->setRootExpr($node);
			}
		}

		return $subjectTypes;
	}
}
