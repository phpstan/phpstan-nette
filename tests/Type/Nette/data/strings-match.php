<?php

namespace StringsMatch;

use Nette\Utils\Strings;
use function PHPStan\Testing\assertType;
use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

function (string $s): void {
	$result = Strings::match($s, '/%env\((.*)\:.*\)%/U');
	assertType('array{string, string}|null', $result);

	$result = Strings::match($s, '/%env\((.*)\:.*\)%/U');
	assertType('array{string, string}|null', $result);

	$result = Strings::match($s, '/(foo)(bar)(baz)/', PREG_OFFSET_CAPTURE);
	assertType('array{array{string, int<0, max>}, array{non-empty-string, int<0, max>}, array{non-empty-string, int<0, max>}, array{non-empty-string, int<0, max>}}|null', $result);

	$result = Strings::match($s, '/(foo)(bar)(baz)/');
	assertType('array{string, non-empty-string, non-empty-string, non-empty-string}|null', $result);

	$result = Strings::match($s, '/(foo)(bar)'. preg_quote($s) .'(baz)/');
	assertType('array{string, non-empty-string, non-empty-string, non-empty-string}|null', $result);
};

function (string $s): void {
	$result = Strings::matchAll($s, '/ab(?P<num>\d+)(?P<suffix>ab)?/', PREG_SET_ORDER);
	assertType("list<array{0: string, num: numeric-string, 1: numeric-string, suffix?: non-empty-string, 2?: non-empty-string}>", $result);
};

function (string $s): void {
	$result = Strings::matchAll($s, '/ab(?P<num>\d+)(?P<suffix>ab)?/', PREG_PATTERN_ORDER);
	assertType("array{0: list<string>, num: list<numeric-string>, 1: list<numeric-string>, suffix: list<string>, 2: list<string>}", $result);
};
