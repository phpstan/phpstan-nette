<?php

$linkGenerator = new \Nette\Application\LinkGenerator();

$linkGenerator->link('***');

$linkGenerator->link('this');
$linkGenerator->link('Test');
$linkGenerator->link('Test:');
$linkGenerator->link('Test:default');

$linkGenerator->link(':Test:default');

$linkGenerator->link('Unknown:default');

$linkGenerator->link('Test:implicit');
$linkGenerator->link('Test:implicit', 'paramValue');
$linkGenerator->link('Test:implicit', ['paramValue']);
$linkGenerator->link('Test:implicit', ['param' => 'paramValue']);

$linkGenerator->link('Test:withParam', ['paramValue']);
$linkGenerator->link('Test:withParam', ['param' => 'paramValue']);

$linkGenerator->link('Test:withParam');
$linkGenerator->link('Test:withParam', 'paramValue');
$linkGenerator->link('Test:withParam', ['paramValue', 'paramValue']);
$linkGenerator->link('Test:withParam', null);
$linkGenerator->link('Test:withParam', [null]);
$linkGenerator->link('Test:withParam', ['param' => null]);

$linkGenerator->link('this!');
$linkGenerator->link('signal!');

$linkGenerator->link('Test:implicit');
