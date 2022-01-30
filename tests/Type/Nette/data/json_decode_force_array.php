<?php

use Nette\Utils\Json;
use function PHPStan\Testing\assertType;

$value = Json::decode('true', Json::FORCE_ARRAY);
assertType('bool', $value);

$value = Json::decode('1', Json::FORCE_ARRAY);
assertType('int', $value);

$value = Json::decode('1.5', Json::FORCE_ARRAY);
assertType('float', $value);

$value = Json::decode('false', Json::FORCE_ARRAY);
assertType('bool', $value);

$value = Json::decode('{}', Json::FORCE_ARRAY);
assertType('array', $value);


function unknownType($mixed) {
	$value = Json::decode($mixed, Json::FORCE_ARRAY);
	assertType('array|bool|float|int|string', $value);
}
