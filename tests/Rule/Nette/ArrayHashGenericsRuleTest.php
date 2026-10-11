<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Rules\Methods\MissingMethodParameterTypehintRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<MissingMethodParameterTypehintRule>
 */
class ArrayHashGenericsRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return self::getContainer()->getByType(MissingMethodParameterTypehintRule::class);
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/array-hash-generics.php'], [
			[
				'Method PHPStan\Rule\Nette\Data\ArrayHashGenerics\Foo::doBar() has parameter $values with generic class ArrayObject but does not specify its types: TKey, TValue',
				18,
			],
		]);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../extension.neon',
		];
	}

}
