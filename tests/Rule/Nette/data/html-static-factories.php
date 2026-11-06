<?php

namespace HtmlStaticFactories;

use Nette\Utils\Html;

function factories(): Html
{
	$text = Html::text('text');
	$html = Html::html('<b>html</b>');
	return $text->addHtml($html);
}
