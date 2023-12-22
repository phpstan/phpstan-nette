<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PHPStan\Analyser\Scope;
use PHPStan\Nette\LinkChecker;
use PHPStan\Rules\Rule;
use PHPStan\Type\Type;

/**
 * @phpstan-template TNodeType of Node
 * @implements Rule<TNodeType>
 */
abstract class LinksRule implements Rule
{

	/** @var LinkChecker */
	protected $linkChecker;

	public function __construct(LinkChecker $linkChecker)
	{
		$this->linkChecker = $linkChecker;
	}

	/**
	 * @return array<string>
	 */
	protected function extractDestintionsFromArg(Scope $scope, ?Arg $arg): array
	{
		if ($arg === null) {
			return [];
		}
		$type = $scope->getType($arg->value);
		$destinations = [];
		foreach ($type->getConstantStrings() as $constantString) {
			$destinations[] = $constantString->getValue();
		}
		return $destinations;
	}

	/**
	 * @return array<array<int|string, Type>>
	 */
	protected function extractParamVariantsFromArrayArg(Scope $scope, ?Arg $arg): array
	{
		if ($arg === null) {
			return [[]];
		}
		$type = $scope->getType($arg->value);
		$paramsVariants = [];
		foreach ($type->getConstantArrays() as $array) {
			$params = [];
			$keyTypes = $array->getKeyTypes();
			$valueTypes = $array->getValueTypes();
			foreach ($keyTypes as $index => $keyType) {
				if ($keyType->isConstantValue()->no()) {
					break;
				}
				$params[$keyType->getValue()] = $valueTypes[$index];
			}
			$paramsVariants[] = $params;
		}

		return $paramsVariants;
	}

	/**
	 * @param array<Arg> $args
	 * @return array<array<int, Type>>
	 */
	protected function extractParamVariantsFromArgs(Scope $scope, array $args): array
	{
		$params = [];
		foreach ($args as $arg) {
			$params[] = $scope->getType($arg->value);
		}
		return [$params];
	}

}
