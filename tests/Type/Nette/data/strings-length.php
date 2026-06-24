<?php

namespace StringsTypesNarrowing;

use Nette\Utils\Strings;
use function PHPStan\Testing\assertType;

function doFoo(string $string) {
	assertType('string', $string);
	if (Strings::length($string)) {
		assertType('non-empty-string', $string);
		assertType('int<1, max>', Strings::length($string));
	} else {
		assertType('string', $string);
		assertType('0', Strings::length($string));
	}
	assertType('string', $string);
	assertType('int', Strings::length($string));

	if (Strings::length($string) === 0) {
		assertType('string', $string);
	}
	assertType('string', $string);
}

/**
 * @param non-empty-string $nonES
 */
function doBar(string $nonES) {
	assertType('int<1, max>', Strings::length($nonES));
}
