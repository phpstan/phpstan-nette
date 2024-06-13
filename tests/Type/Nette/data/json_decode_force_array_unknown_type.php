<?php

use Nette\Utils\Json;
use function PHPStan\Testing\assertType;

function unknownType($mixed) {
	$value = Json::decode($mixed, Json::FORCE_ARRAY);
	assertType('array|bool|float|int|string', $value);
}
