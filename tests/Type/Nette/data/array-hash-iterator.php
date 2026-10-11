<?php

namespace PHPStan\Type\Nette\Data\ArrayHashIterator;

use Nette\Utils\ArrayHash;
use function PHPStan\Testing\assertType;

/**
 * @param ArrayHash<int> $hash
 */
function test(ArrayHash $hash): void
{
	foreach ($hash as $key => $value) {
		assertType('(int|string)', $key);
		assertType('int', $value);
	}
}
