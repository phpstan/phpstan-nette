<?php
declare(strict_types=1);

use Nette\Utils\Strings;

function withoutPriorCheck(string $body): void
{
	if (Strings::length($body) < 100) { // no error
		echo 'a';
	}
}

function withPriorCheck(string $body): void
{
	if ($body !== '' && Strings::length($body) < 100) { // false positive
		echo 'a';
	}
}
