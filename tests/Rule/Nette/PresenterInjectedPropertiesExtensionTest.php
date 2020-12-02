<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

class PresenterInjectedPropertiesExtensionTest extends \PHPStan\Testing\LevelsTestCase
{

	public function dataTopics(): array
	{
		return [
			['presenterInject'],
		];
	}

	public function getDataPath(): string
	{
		return __DIR__ . '/data';
	}

	public function getPhpStanExecutablePath(): string
	{
		return __DIR__ . '/../../../vendor/bin/phpstan';
	}

	public function getPhpStanConfigPath(): ?string
	{
		return __DIR__ . '/phpstan.neon';
	}

}
