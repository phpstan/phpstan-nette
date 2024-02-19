<?php

use Nette\DI\Attributes\Inject;

class Service
{

}

class InjectAttributePresenter
{
	#[Inject]
	public Service $service;
}
