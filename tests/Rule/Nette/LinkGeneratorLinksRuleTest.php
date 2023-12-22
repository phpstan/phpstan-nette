<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PHPStan\Nette\ContainerResolver;
use PHPStan\Nette\LinkChecker;
use PHPStan\Nette\PresenterResolver;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<LinkGeneratorLinksRule>
 */
class LinkGeneratorLinksRuleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new LinkGeneratorLinksRule(
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
		$this->analyse([__DIR__ . '/data/links-linkGenerator.php'], [
			[
				'Invalid link destination \'***\' in link() call.',
				5,
			],
			[
				'Invalid link destination \'this\' in link() call.',
				7,
			],
			[
				'Invalid link destination \'Test\' in link() call.',
				8,
			],
			[
				'Invalid link destination \':Test:default\' in link() call: Do not use absolute destinations with LinkGenerator.',
				12,
			],
			[
				'Invalid link destination \'Unknown:default\' in link() call: Cannot load presenter \'Unknown\', class \'PHPStan\TestApp\Presenters\UnknownPresenter\' was not found.',
				14,
			],
			[
				'Invalid link params in link() call: Unable to pass parameters to action \':Test:implicit\', missing corresponding method in PHPStan\TestApp\Presenters\TestPresenter.',
				18,
			],
			[
				'Invalid link params in link() call: Passed more parameters than method PHPStan\TestApp\Presenters\TestPresenter::actionWithParam() expects.',
				26,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\TestPresenter::actionWithParam() must be string, null given.',
				28,
			],
			[
				'Invalid link params in link() call: Argument $param passed to PHPStan\TestApp\Presenters\TestPresenter::actionWithParam() must be string, null given.',
				29,
			],
			[
				'Invalid link destination \'this!\' in link() call.',
				31,
			],
			[
				'Invalid link destination \'signal!\' in link() call.',
				32,
			],
		]);
	}

}
