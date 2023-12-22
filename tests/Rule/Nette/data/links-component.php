<?php

$component = new \PHPStan\TestApp\Components\CurrentComponent();

$component->link('***');

$component->link('Test');
$component->link(':Test');

$component->link('this');
$component->link('signal');
$component->link('subComponent-signal');
$component->link('unknown');
$component->link('subComponent-unknown');
$component->link('genericComponent-unknown');
$component->link('unknownComponent-signal');

$component->link('this!');
$component->link('signal!');
$component->link('subComponent-signal!');
$component->link('unknown!');
$component->link('subComponent-unknown!');
$component->link('genericComponent-unknown!');
$component->link('unknownComponent-signal!');

$component->link('withParam');
$component->link('withParam', null);
$component->link('withParam', [null]);
$component->link('withParam', ['param' => null]);

$component->lazyLink('***');
$component->isLinkCurrent('***');
if(false) {$component->redirect('***');}
if(false) {$component->redirectPermanent('***');}
