<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Rules\Methods\CallStaticMethodsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CallStaticMethodsRule>
 */
class HtmlStaticFactoriesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(CallStaticMethodsRule::class); // @phpstan-ignore phpstanApi.classConstant
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/html-static-factories.php'], []);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../extension.neon',
		];
	}

}
