<?php

use Nette\Utils\Json;
use function PHPStan\Testing\assertType;

function unknownType($mixed) {
	$value = Json::decode($mixed);
	assertType('bool|float|int|stdClass|string', $value);
}
