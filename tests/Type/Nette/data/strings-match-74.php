<?php

namespace StringsMatch;

use Nette\Utils\Strings;
use function PHPStan\Testing\assertType;

function (string $s): void {
	Strings::replace(
		$s,
		'/(foo)?(bar)?(baz)?/',
		function ($matches) {
			assertType('array{string, \'foo\'|null, \'bar\'|null, \'baz\'|null}', $matches);
			return '';
		},
		-1,
		false,
		true
	);
};

function (string $s): void {
	Strings::replace(
		$s,
		'/(foo)?(bar)?(baz)?/',
		function ($matches) {
			assertType('list{0: array{string, int<-1, max>}, 1?: array{\'\'|\'foo\', int<-1, max>}, 2?: array{\'\'|\'bar\', int<-1, max>}, 3?: array{\'baz\', int<-1, max>}}', $matches);
			return '';
		},
		-1,
		true
	);
};

function (string $s): void {
	Strings::replace(
		$s,
		'/(foo)?(bar)?(baz)?/',
		function ($matches) {
			assertType('array{array{string|null, int<-1, max>}, array{\'foo\'|null, int<-1, max>}, array{\'bar\'|null, int<-1, max>}, array{\'baz\'|null, int<-1, max>}}', $matches);
			return '';
		},
		-1,
		true,
		true
	);
};

function (string $s): void {
	$result = Strings::replace(
		$s,
		'/(foo)?(bar)?(baz)?/',
		'bee'
	);
	assertType('string', $result);
};
