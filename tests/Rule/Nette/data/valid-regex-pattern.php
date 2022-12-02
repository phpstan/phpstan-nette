<?php

$string = (function (): string {})();

\Nette\Utils\Strings::match('', '~ok~');
\Nette\Utils\Strings::match('', 'nok');
\Nette\Utils\Strings::match('', '~(~');
\Nette\Utils\Strings::match('', $string);

\Nette\Utils\Strings::matchAll('', '~ok~');
\Nette\Utils\Strings::matchAll('', 'nok');
\Nette\Utils\Strings::matchAll('', '~(~');
\Nette\Utils\Strings::matchAll('', $string);

\Nette\Utils\Strings::split('', '~ok~');
\Nette\Utils\Strings::split('', 'nok');
\Nette\Utils\Strings::split('', '~(~');
\Nette\Utils\Strings::split('', $string);

\Nette\Utils\Strings::replace('', '~ok~', '');
\Nette\Utils\Strings::replace('', 'nok', '');
\Nette\Utils\Strings::replace('', '~(~', '');
\Nette\Utils\Strings::replace('', $string, '');
\Nette\Utils\Strings::replace('', ['~ok~', 'nok', '~(~', $string], '');

\Nette\Utils\Strings::replace(
	'',
	[
		'~ok~' => function () {},
		'nok' => function () {},
		'~(~' => function () {},
	]
);
