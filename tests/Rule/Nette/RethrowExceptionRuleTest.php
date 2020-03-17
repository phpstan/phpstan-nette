<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use AbortException;
use FooPresenter;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RethrowExceptionRule>
 */
class RethrowExceptionRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new RethrowExceptionRule(
			[FooPresenter::class => ['redirect' => AbortException::class]]
		);
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/rethrow-abort.php'], [
			[
				'Exception AbortException needs to be rethrown.',
				8,
			],
			[
				'Exception AbortException needs to be rethrown.',
				14,
			],
			[
				'Exception AbortException needs to be rethrown.',
				76,
			],
			[
				'Exception AbortException needs to be rethrown.',
				95,
			],
		]);
	}

}
