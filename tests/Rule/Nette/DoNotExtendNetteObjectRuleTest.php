<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use function class_exists;

/**
 * @extends RuleTestCase<DoNotExtendNetteObjectRule>
 */
class DoNotExtendNetteObjectRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new DoNotExtendNetteObjectRule();
	}

	public function testSmartObjectChild(): void
	{
		$this->analyse([__DIR__ . '/../../SmartObjectChild.php'], []);
	}

	public function testNetteObjectChild(): void
	{
		if (!class_exists('Nette\LegacyObject')) {
			self::markTestSkipped('LegacyObject does no longer exist.');
		}
		$this->analyse([__DIR__ . '/../../NetteObjectChild.php'], [
			[
				'Class PHPStan\NetteObjectChild extends Nette\Object - it\'s better to use Nette\SmartObject trait.',
				5,
			],
		]);
	}

}
