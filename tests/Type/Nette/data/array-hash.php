<?php

namespace PHPStan\Type\Nette\Data\ArrayHash;

use Nette\Utils\ArrayHash;
use function PHPStan\Testing\assertType;

/**
 * @param ArrayHash<int> $hash
 */
function test(ArrayHash $hash): void
{
	assertType('Nette\\Utils\\ArrayHash<int>', $hash);
	assertType('int', $hash['foo']);
	assertType('int', $hash->offsetGet('foo'));
	assertType('mixed', $hash->foo);
}
