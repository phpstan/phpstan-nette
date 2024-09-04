<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\Strings;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\TrinaryLogic;
use PHPStan\Type\ClosureType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\StaticMethodParameterClosureTypeExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use function array_key_exists;
use const PREG_OFFSET_CAPTURE;
use const PREG_UNMATCHED_AS_NULL;

final class StringsReplaceCallbackClosureTypeExtension implements StaticMethodParameterClosureTypeExtension
{

	private RegexArrayShapeMatcher $regexArrayShapeMatcher;

	public function __construct(RegexArrayShapeMatcher $regexArrayShapeMatcher)
	{
		$this->regexArrayShapeMatcher = $regexArrayShapeMatcher;
	}

	public function isStaticMethodSupported(MethodReflection $methodReflection, ParameterReflection $parameter): bool
	{
		return $methodReflection->getDeclaringClass()->getName() === Strings::class
			&& $methodReflection->getName() === 'replace'
			&& $parameter->getName() === 'replacement';
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, ParameterReflection $parameter, Scope $scope): ?Type
	{
		$args = $methodCall->getArgs();
		$patternArg = $args[1] ?? null;
		$replacementArg = $args[2] ?? null;

		if ($patternArg === null || $replacementArg === null) {
			return null;
		}

		$replacementType = $scope->getType($replacementArg->value);

		if (!$replacementType->isCallable()->yes()) {
			return null;
		}

		$matchesType = $this->regexArrayShapeMatcher->matchExpr(
			$patternArg->value,
			$this->resolveFlagsType($args, $scope),
			TrinaryLogic::createYes(),
			$scope,
		);

		if ($matchesType === null) {
			return null;
		}

		return new ClosureType(
			[
				$this->createParameterReflectionClass($parameter, $matchesType),
			],
			new StringType(),
		);
	}

	/**
	 * @param array<Arg> $args
	 */
	private function resolveFlagsType(array $args, Scope $scope): ConstantIntegerType
	{
		$captureOffsetType = array_key_exists(4, $args) ? $scope->getType($args[4]->value) : new ConstantBooleanType(false);
		$unmatchedAsNullType = array_key_exists(5, $args) ? $scope->getType($args[5]->value) : new ConstantBooleanType(false);

		$captureOffset = $captureOffsetType->isTrue()->yes();
		$unmatchedAsNull = $unmatchedAsNullType->isTrue()->yes();

		return new ConstantIntegerType(($captureOffset ? PREG_OFFSET_CAPTURE : 0) | ($unmatchedAsNull ? PREG_UNMATCHED_AS_NULL : 0));
	}

	private function createParameterReflectionClass(ParameterReflection $parameter, Type $matchesType): ParameterReflection
	{
		return new class($parameter, $matchesType) implements ParameterReflection {

			private ParameterReflection $parameter;

			private Type $matchesType;

			public function __construct(
				ParameterReflection $parameter,
				Type $matchesType
			)
			{
				$this->parameter = $parameter;
				$this->matchesType = $matchesType;
			}

			public function getName(): string
			{
				return $this->parameter->getName();
			}

			public function isOptional(): bool
			{
				return $this->parameter->isOptional();
			}

			public function getType(): Type
			{
				return $this->matchesType;
			}

			public function passedByReference(): PassedByReference
			{
				return $this->parameter->passedByReference();
			}

			public function isVariadic(): bool
			{
				return $this->parameter->isVariadic();
			}

			public function getDefaultValue(): ?Type
			{
				return $this->parameter->getDefaultValue();
			}

		};
	}

}
