<?php

use Timber\Timber;
use GainTest\IndexController;

$page = IndexController::indexAction();

Timber::render($page['templates'], $page['context']);