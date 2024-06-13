<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Nette\ContainerResolver;
use PHPStan\Nette\LinkChecker;
use PHPStan\Nette\PresenterResolver;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<PresenterLinksRule>
 */
class PresenterLinksRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new PresenterLinksRule(
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

	public function testRuleForPresenter(): void
	{
		require_once __DIR__ . '/../../TestApp/autoload.php';
		$this->analyse([__DIR__ . '/data/links-presenter.php'], [
			[
				'Invalid link destination \'***\' in link() call.',
				5,
			],
			[
				'Invalid link destination \':Unknown:default\' in link() call: Cannot load presenter \'Unknown\', class \'PHPStan\TestApp\Presenters\UnknownPresenter\' was not found.',
				12,
			],
			[
				'Invalid link params in link() call: Unable to pass parameters to action \':Test:implicit\', missing corresponding method in PHPStan\TestApp\Presenters\TestPresenter.',
				15,
			],
			[
				'Invalid link params in link() call: Unable to pass parameters to action \':Test:implicit\', missing corresponding method in PHPStan\TestApp\Presenters\TestPresenter.',
				16,
			],
			[
				'Invalid link params in link() call: Passed more parameters than method PHPStan\TestApp\Presenters\TestPresenter::actionWithParam() expects.',
				24,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\TestPresenter::actionWithParam() must be string, null given.',
				25,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\TestPresenter::actionWithParam() must be string, null given.',
				26,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\TestPresenter::actionWithParam() must be string, null given.',
				27,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\CurrentModule\CurrentPresenter::actionWithParam() must be int, null given.',
				30,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\CurrentModule\CurrentPresenter::actionWithParam() must be int, null given.',
				31,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\CurrentModule\CurrentPresenter::actionWithParam() must be int, null given.',
				32,
			],
			[
				'Invalid link destination \'unknown!\' in link() call: Unknown signal \'unknown\', missing handler PHPStan\TestApp\Presenters\CurrentModule\CurrentPresenter::handleUnknown()',
				37,
			],
			[
				'Invalid link destination \'subComponent-unknown!\' in link() call: Unknown signal \'unknown\', missing handler PHPStan\TestApp\Components\CurrentComponent::handleUnknown()',
				38,
			],
			[
				'Invalid link destination \'!\' in link() call.',
				39,
			],
			[
				'Invalid link destination \'*!\' in link() call.',
				40,
			],
			[
				'Invalid link destination \'***\' in lazyLink() call.',
				42,
			],
			[
				'Invalid link destination \'***\' in isLinkCurrent() call.',
				43,
			],
			[
				'Invalid link destination \'***\' in redirect() call.',
				44,
			],
			[
				'Invalid link destination \'***\' in redirectPermanent() call.',
				45,
			],
			[
				'Invalid link destination \'***\' in forward() call.',
				46,
			],
			[
				'Invalid link destination \'***\' in canonicalize() call.',
				47,
			],
			[
				'Link check failed: Cannot analyse relative destination \'Test:default\' for abstract class Nette\Application\UI\Presenter.',
				58,
			],
		]);
	}

}
