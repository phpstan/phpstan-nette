<?php

namespace StringsTypesNarrowing;

use Nette\Utils\Strings;
use function PHPStan\Testing\assertType;

function doFoo(string $string) {
	assertType('string', $string);
	if (Strings::length($string)) {
		assertType('non-empty-string', $string);
	} else {
		assertType('string', $string);
	}
	assertType('string', $string);

	if (Strings::length($string) === 0) {
		assertType('string', $string);
	}
	assertType('string', $string);
}
