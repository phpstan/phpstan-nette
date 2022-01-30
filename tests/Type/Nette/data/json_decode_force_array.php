<?php

use Nette\Utils\Json;
use function PHPStan\Testing\assertType;

$value = Json::decode('true', Json::FORCE_ARRAY);
assertType('true', $value);

$value = Json::decode('1', Json::FORCE_ARRAY);
assertType('1', $value);

$value = Json::decode('1.5', Json::FORCE_ARRAY);
assertType('1.5', $value);

$value = Json::decode('false', Json::FORCE_ARRAY);
assertType('false', $value);

$value = Json::decode('{}', Json::FORCE_ARRAY);
assertType('array{}', $value);

$value = Json::decode('[1, 2, 3]', Json::FORCE_ARRAY);
assertType('array{1, 2, 3}', $value);


function unknownType($mixed) {
	$value = Json::decode($mixed, Json::FORCE_ARRAY);
	assertType('array|bool|float|int|string', $value);
}
