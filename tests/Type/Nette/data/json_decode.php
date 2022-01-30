<?php

use Nette\Utils\Json;
use function PHPStan\Testing\assertType;

$value = Json::decode('true');
assertType('true', $value);

$value = Json::decode('1');
assertType('1', $value);

$value = Json::decode('1.5');
assertType('1.5', $value);

$value = Json::decode('false');
assertType('false', $value);

$value = Json::decode('{}');
assertType('stdClass', $value);

$value = Json::decode('[1, 2, 3]');
assertType('array{1, 2, 3}', $value);


function unknownType($mixed) {
	$value = Json::decode($mixed);
	assertType('bool|float|int|stdClass|string', $value);
}


