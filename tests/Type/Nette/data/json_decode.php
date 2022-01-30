<?php

use Nette\Utils\Json;
use function PHPStan\Testing\assertType;

$value = Json::decode('true');
assertType('bool', $value);

$value = Json::decode('1');
assertType('int', $value);

$value = Json::decode('1.5');
assertType('float', $value);

$value = Json::decode('false');
assertType('bool', $value);

function unknownType($mixed) {
	$value = Json::decode($mixed);
	assertType('bool|float|int|stdClass|string', $value);
}


