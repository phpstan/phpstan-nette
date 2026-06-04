<?php

namespace StringsMatchSubject;

use Nette\Utils\Strings;
use function PHPStan\Testing\assertType;

function (string $s): void {
	if (Strings::match($s, '/foo/')) {
		assertType("non-falsy-string", $s);
	} else {
		assertType("string", $s);
	}
	assertType("string", $s);

	$matches = Strings::matchAll($s, '/foo/');
	if (count($matches) !== 0) {
		assertType("non-falsy-string", $s);
	} else {
		assertType("string", $s);
	}
	assertType("string", $s);
};

function ($mixed): void {
	if (Strings::match($mixed, '/foo/')) {
		assertType("mixed", $mixed);
	} else {
		assertType("mixed", $mixed);
	}
	assertType("mixed", $mixed);
};
