<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use Nette\Utils\RegexpException;
use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function in_array;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<Node\Expr\StaticCall>
 */
class RegularExpressionPatternRule implements Rule
{

	public function getNodeType(): string
	{
		return StaticCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		$patterns = $this->extractPatterns($node, $scope);

		$errors = [];
		foreach ($patterns as $pattern) {
			$errorMessage = $this->validatePattern($pattern);
			if ($errorMessage === null) {
				continue;
			}

			$errors[] = RuleErrorBuilder::message(sprintf('Regex pattern is invalid: %s', $errorMessage))
				->identifier('regexp.pattern')
				->build();
		}

		return $errors;
	}

	/**
	 * @return string[]
	 */
	private function extractPatterns(StaticCall $staticCall, Scope $scope): array
	{
		if (!$staticCall->class instanceof Node\Name || !$staticCall->name instanceof Node\Identifier) {
			return [];
		}
		$className = $scope->resolveName($staticCall->class);
		if ($className !== Strings::class) {
			return [];
		}
		$methodName = strtolower((string) $staticCall->name);
		if (
			!in_array($methodName, [
				'split',
				'match',
				'matchall',
				'replace',
			], true)
		) {
			return [];
		}

		if (!isset($staticCall->getArgs()[1])) {
			return [];
		}
		$patternNode = $staticCall->getArgs()[1]->value;
		$patternType = $scope->getType($patternNode);

		$patternStrings = [];

		foreach ($patternType->getConstantStrings() as $constantStringType) {
			$patternStrings[] = $constantStringType->getValue();
		}

		foreach ($patternType->getConstantArrays() as $constantArrayType) {
			if ($methodName !== 'replace') {
				continue;
			}

			foreach ($constantArrayType->getKeyTypes() as $arrayKeyType) {
				foreach ($arrayKeyType->getConstantStrings() as $constantString) {
					$patternStrings[] = $constantString->getValue();
				}
			}
		}

		return $patternStrings;
	}

	private function validatePattern(string $pattern): ?string
	{
		try {
			Strings::match('', $pattern);
		} catch (RegexpException $e) {
			return $e->getMessage();
		}

		return null;
	}

}
