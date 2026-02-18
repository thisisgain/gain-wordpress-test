<?php

namespace GainTest\Wordpress;

use Timber\Timber;

/**
 * Class SingleController
 *
 * Handles requests that go through the index.php file
 */
class SingleController
{
    /**
     * Index action
     *
     * Used as the "posts" page action in index.php
     *
     * @return array
     */
    public static function postAction()
    {
        $context = Timber::context();

        return [
            'templates' => ['views/single.twig'],
            'context' => $context
        ];
    }
}
