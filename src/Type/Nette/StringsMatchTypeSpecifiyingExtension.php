<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;
use function in_array;

class StringsMatchTypeSpecifiyingExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
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
		return $context->true() && in_array($staticMethodReflection->getName(), ['match', 'matchAll'], true);
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
