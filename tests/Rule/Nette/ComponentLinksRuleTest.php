<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Nette\ContainerResolver;
use PHPStan\Nette\LinkChecker;
use PHPStan\Nette\PresenterResolver;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ComponentLinksRule>
 */
class ComponentLinksRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ComponentLinksRule(
			new LinkChecker(
				new PresenterResolver(
					['*' => 'PHPStan\TestApp\Presenters\*\*Presenter'],
					new ContainerResolver(null),
					self::getContainer()->getByType(ReflectionProvider::class)
				),
				self::getContainer()->getByType(ReflectionProvider::class)
			)
		);
	}

	public function testRule(): void
	{
		require_once __DIR__ . '/../../TestApp/autoload.php';
		$this->analyse([__DIR__ . '/data/links-component.php'], [
			[
				'Invalid link destination \'***\' in link() call.',
				5,
			],
			[
				'Invalid link destination \'Test\' in link() call: Unknown signal \'Test\', missing handler PHPStan\TestApp\Components\CurrentComponent::handleTest()',
				7,
			],
			[
				'Invalid link destination \':Test\' in link() call: Unknown signal \'Test\', missing handler PHPStan\TestApp\Components\CurrentComponent::handleTest()',
				8,
			],
			[
				'Invalid link destination \'unknown\' in link() call: Unknown signal \'unknown\', missing handler PHPStan\TestApp\Components\CurrentComponent::handleUnknown()',
				13,
			],
			[
				'Invalid link destination \'subComponent-unknown\' in link() call: Unknown signal \'unknown\', missing handler PHPStan\TestApp\Components\CurrentComponent::handleUnknown()',
				14,
			],
			[
				'Invalid link destination \'unknownComponent-signal\' in link() call: Sub-component \'unknownComponent\' might not exists. Method PHPStan\TestApp\Components\CurrentComponent::createComponentUnknownComponent() not found.',
				16,
			],
			[
				'Invalid link destination \'unknown!\' in link() call: Unknown signal \'unknown\', missing handler PHPStan\TestApp\Components\CurrentComponent::handleUnknown()',
				21,
			],
			[
				'Invalid link destination \'subComponent-unknown!\' in link() call: Unknown signal \'unknown\', missing handler PHPStan\TestApp\Components\CurrentComponent::handleUnknown()',
				22,
			],
			[
				'Invalid link destination \'unknownComponent-signal!\' in link() call: Sub-component \'unknownComponent\' might not exists. Method PHPStan\TestApp\Components\CurrentComponent::createComponentUnknownComponent() not found.',
				24,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Components\CurrentComponent::handleWithParam() must be int, null given.',
				27,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Components\CurrentComponent::handleWithParam() must be int, null given.',
				28,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Components\CurrentComponent::handleWithParam() must be int, null given.',
				29,
			],
			[
				'Invalid link destination \'***\' in lazyLink() call.',
				31,
			],
			[
				'Invalid link destination \'***\' in isLinkCurrent() call.',
				32,
			],
			[
				'Invalid link destination \'***\' in redirect() call.',
				33,
			],
			[
				'Invalid link destination \'***\' in redirectPermanent() call.',
				34,
			],
		]);
	}

}
