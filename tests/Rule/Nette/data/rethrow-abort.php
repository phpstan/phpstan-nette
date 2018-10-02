<?php

class FooPresenter extends \Nette\Application\UI\Presenter
{

	public function doFoo()
	{
		try {
			$this->redirect('this');
		} catch (\Throwable $e) {

		}

		try {
			$this->redirect('this');
		} catch (\Exception $e) {

		}
	}

	public function doBar()
	{
		try {
			$this->redirect('this'); // OK
		} catch (\InvalidArgumentException $e) {

		}
	}

	public function doIpsum()
	{
		try {
			$this->redirect('this'); // OK
		} catch (\Nette\Application\AbortException $e) {
			throw $e;
		}
	}

	public function doBaz()
	{
		try {
			$this->redirect('this'); // OK
		} catch (\Nette\Application\AbortException | \InvalidArgumentException $e) {
			throw $e;
		}
	}

	public function doLorem()
	{
		try {
			$this->redirect('this'); // OK
		} catch (\Nette\Application\AbortException $e) {
			throw $e;
		} catch (\Throwable $e) {

		}

		try {
			$this->redirect('this'); // OK
		} catch (\Nette\Application\AbortException $e) {
			throw $e;
		} catch (\Exception $e) {

		}

		try {
			$this->redirect('this'); // OK
		} catch (\InvalidArgumentException $e) {

		} catch (\Nette\Application\AbortException $e) {
			throw $e;
		} catch (\Exception $e) {

		}

		try {
			$this->redirect('this');
		} catch (\InvalidArgumentException $e) {

		} catch (\Exception $e) {

		}

		$this->redirect('this'); // OK, outside of try
	}

	public function doDolor()
	{
		try {
			$this->getAction();
		} catch (\Exception $e) {

		}

		try {
			$this->redirect('this');
		} catch (\Nette\Application\AbortException $e) {
			// does not rethrow
		} catch (\Throwable $e) {

		}

		try {
			$this->redirect('this');
		} catch (\Throwable $e) {
			throw $e;
		}

		try {
			$this->redirect('this');
		} catch (\Exception $e) {
			throw $e;
		}
	}

}
