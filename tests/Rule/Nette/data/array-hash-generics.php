<?php

namespace PHPStan\Rule\Nette\Data\ArrayHashGenerics;

use Nette\Utils\ArrayHash;

class Foo
{

	public function doFoo(ArrayHash $values): ArrayHash
	{
		return $values;
	}

	/**
	 * @param \ArrayObject $values
	 */
	public function doBar(\ArrayObject $values): void
	{
	}

}
