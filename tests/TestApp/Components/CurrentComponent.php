<?php declare(strict_types = 1);

namespace PHPStan\TestApp\Components;

use Nette\Application\UI\Component;

class CurrentComponent extends Component
{

	public function handleSignal(): void
	{
	}

	public function handleWithParam(int $param): void
	{
	}

	public function createComponentSubComponent(): self
	{
		return new self();
	}

	public function createComponentGenericComponent(): Component
	{
		return new self();
	}

}
