<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use function sprintf;
use const PHP_VERSION_ID;

/**
 * @extends RuleTestCase<RegularExpressionPatternRule>
 */
class RegularExpressionPatternRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new RegularExpressionPatternRule();
	}

	public function testValidRegexPatternAfter73(): void
	{
		$messagePart = 'alphanumeric or backslash';
		if (PHP_VERSION_ID >= 80200) {
			$messagePart = 'alphanumeric, backslash, or NUL';
		}
		if (PHP_VERSION_ID >= 80400) {
			$messagePart = 'alphanumeric, backslash, or NUL byte';
		}

		$this->analyse(
			[__DIR__ . '/data/valid-regex-pattern.php'],
			[
				[
					sprintf('Regex pattern is invalid: Delimiter must not be %s in pattern: nok', $messagePart),
					6,
				],
				[
					'Regex pattern is invalid: Compilation failed: missing closing parenthesis at offset 1 in pattern: ~(~',
					7,
				],
				[
					sprintf('Regex pattern is invalid: Delimiter must not be %s in pattern: nok', $messagePart),
					11,
				],
				[
					'Regex pattern is invalid: Compilation failed: missing closing parenthesis at offset 1 in pattern: ~(~',
					12,
				],
				[
					sprintf('Regex pattern is invalid: Delimiter must not be %s in pattern: nok', $messagePart),
					16,
				],
				[
					'Regex pattern is invalid: Compilation failed: missing closing parenthesis at offset 1 in pattern: ~(~',
					17,
				],
				[
					sprintf('Regex pattern is invalid: Delimiter must not be %s in pattern: nok', $messagePart),
					21,
				],
				[
					'Regex pattern is invalid: Compilation failed: missing closing parenthesis at offset 1 in pattern: ~(~',
					22,
				],
				[
					sprintf('Regex pattern is invalid: Delimiter must not be %s in pattern: nok', $messagePart),
					26,
				],
				[
					'Regex pattern is invalid: Compilation failed: missing closing parenthesis at offset 1 in pattern: ~(~',
					26,
				],
			],
		);
	}

}
