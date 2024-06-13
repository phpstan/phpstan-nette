<?php declare(strict_types = 1);

namespace PHPStan\TestApp\Presenters;

use Nette\Application\UI\Presenter;

class TestPresenter extends Presenter
{

	public function actionDefault(): void
	{
	}

	public function actionWithParam(string $param): void
	{
	}

}
