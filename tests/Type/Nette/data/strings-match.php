<?php

namespace StringsMatch;

use Nette\Utils\Strings;
use function PHPStan\Testing\assertType;
use const PREG_OFFSET_CAPTURE;

function (string $s): void {
	$result = Strings::match($s, '/%env\((.*)\:.*\)%/U');
	assertType('array{string, string}|null', $result);

	$result = Strings::match($s, '/%env\((.*)\:.*\)%/U');
	assertType('array{string, string}|null', $result);

	$result = Strings::match($s, '/(foo)(bar)(baz)/', PREG_OFFSET_CAPTURE);
	assertType('array{array{string, int<0, max>}, array{string, int<0, max>}, array{string, int<0, max>}, array{string, int<0, max>}}|null', $result);

	$result = Strings::match($s, '/(foo)(bar)(baz)/');
	assertType('array{string, string, string, string}|null', $result);

	$result = Strings::match($s, '/(foo)(bar)'. preg_quote($s) .'(baz)/');
	assertType('array{string, string, string, string}|null', $result);
};
