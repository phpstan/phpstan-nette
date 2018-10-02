<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class RethrowExceptionRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new RethrowExceptionRule(
			[Presenter::class => ['redirect' => AbortException::class]]
		);
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/rethrow-abort.php'], [
			[
				'Exception Nette\Application\AbortException needs to be rethrown.',
				8,
			],
			[
				'Exception Nette\Application\AbortException needs to be rethrown.',
				14,
			],
			[
				'Exception Nette\Application\AbortException needs to be rethrown.',
				76,
			],
			[
				'Exception Nette\Application\AbortException needs to be rethrown.',
				95,
			],
		]);
	}

}
