<?php

namespace StringsMatch;

use Nette\Utils\Strings;
use function PHPStan\Testing\assertType;
use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

function (string $s): void {
	$result = Strings::match($s, '/%env\((.*)\:.*\)%/U');
	assertType('array{non-falsy-string, string}|null', $result);

	$result = Strings::match($s, '/%env\((.*)\:.*\)%/U');
	assertType('array{non-falsy-string, string}|null', $result);

	$result = Strings::match($s, '/(foo)(bar)(baz)/', PREG_OFFSET_CAPTURE);
	assertType("array{array{non-falsy-string, int<-1, max>}, array{'foo', int<-1, max>}, array{'bar', int<-1, max>}, array{'baz', int<-1, max>}}|null", $result);

	$result = Strings::match($s, '/(foo)(bar)(baz)/');
	assertType("array{non-falsy-string, 'foo', 'bar', 'baz'}|null", $result);

	$result = Strings::match($s, '/(foo)(bar)'. preg_quote($s) .'(baz)/');
	assertType("array{non-falsy-string, 'foo', 'bar', 'baz'}|null", $result);
};

function (string $s): void {
	$result = Strings::matchAll($s, '/ab(?P<num>\d+)(?P<suffix>ab)?/', PREG_SET_ORDER);
	assertType("list<array{0: string, num: numeric-string, 1: numeric-string, suffix?: 'ab', 2?: 'ab'}>", $result);
};

function (string $s): void {
	$result = Strings::matchAll($s, '/ab(?P<num>\d+)(?P<suffix>ab)?/', PREG_PATTERN_ORDER);
	assertType("array{0: list<string>, num: list<numeric-string>, 1: list<numeric-string>, suffix: list<''|'ab'>, 2: list<''|'ab'>}", $result);
};

function (string $s): void {
	$result = Strings::matchAll($s, '/ab(?P<num>\d+)(?P<suffix>ab)?/', false, 0, false, true);
	assertType("array{0: list<string>, num: list<numeric-string>, 1: list<numeric-string>, suffix: list<''|'ab'>, 2: list<''|'ab'>}", $result);
};

function (string $s): void {
	$result = Strings::matchAll($s, '~\[gallery ids=(„|")(?<allIds>([0-9]+,? ?)+)(“|")~');
	assertType("list<array{0: string, 1: '\"'|'„', allIds: non-empty-string, 2: non-empty-string, 3: non-empty-string, 4: '\"'|'“'}>", $result);
};
