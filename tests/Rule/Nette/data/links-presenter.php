<?php

$presenter = new \PHPStan\TestApp\Presenters\CurrentModule\CurrentPresenter();

$presenter->link('***');

$presenter->link('this');
$presenter->link('Test:');
$presenter->link('Test:default');
$presenter->link('default');

$presenter->link(':Unknown:default');

$presenter->link(':Test:implicit');
$presenter->link(':Test:implicit', 'paramValue');
$presenter->link(':Test:implicit', ['paramValue']);
$presenter->link(':Test:implicit', ['param' => 'paramValue']);

$presenter->link(':Test:withParam', 'paramValue');
$presenter->link(':Test:withParam', ['paramValue']);
$presenter->link(':Test:withParam', ['param' => 'paramValue']);

$presenter->link(':Test:withParam');
$presenter->link(':Test:withParam', 'paramValue', 'paramValue');
$presenter->link(':Test:withParam', null);
$presenter->link(':Test:withParam', [null]);
$presenter->link(':Test:withParam', ['param' => null]);

$presenter->link('withParam');
$presenter->link('withParam', null);
$presenter->link('withParam', [null]);
$presenter->link('withParam', ['param' => null]);

$presenter->link('this!');
$presenter->link('signal!');
$presenter->link('subComponent-signal!');
$presenter->link('unknown!');
$presenter->link('subComponent-unknown!');
$presenter->link('!');
$presenter->link('*!');

$presenter->lazyLink('***');
$presenter->isLinkCurrent('***');
if(false) {$presenter->redirect('***');}
if(false) {$presenter->redirectPermanent('***');}
if(false) {$presenter->forward('***');}
if(false) {$presenter->canonicalize('***');}

$presenter->isLinkCurrent(':Test:*');
$presenter->isLinkCurrent('Test:*');
$presenter->isLinkCurrent('*');

$presenter->link(':Test:implicit');

$abstractPresenter = $presenter->getPresenter();
$abstractPresenter->link('this');
$abstractPresenter->link(':Test:default');
$abstractPresenter->link('Test:default');
