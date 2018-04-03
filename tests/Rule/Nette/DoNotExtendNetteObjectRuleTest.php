<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Rules\Rule;

class DoNotExtendNetteObjectRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): Rule
	{
		return new DoNotExtendNetteObjectRule($this->createBroker());
	}

	public function testSmartObjectChild(): void
	{
		$this->analyse([__DIR__ . '/../../SmartObjectChild.php'], []);
	}

	public function testNetteObjectChild(): void
	{
		$this->analyse([__DIR__ . '/../../NetteObjectChild.php'], [
			[
				'Class PHPStan\NetteObjectChild extends Nette\Object - it\'s better to use Nette\SmartObject trait.',
				5,
			],
		]);
	}

}
