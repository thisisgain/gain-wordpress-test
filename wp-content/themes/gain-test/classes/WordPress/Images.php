<?php

namespace GainTest\WordPress;

use NanoSoup\Zeus\Wordpress\Images as ZeusImages;
use Timber\Timber;

class Images extends ZeusImages
{
    public function __construct()
    {
        add_action('after_setup_theme', [$this, 'customImageSizes']);
    }

    /**
     * @param $string
     * @param int
     * @param int
     * 
     * Example:
     * $this->addImageSize('hero-banner-image', 1500, 925);
     */
    public function customImageSizes()
    {
    }
}
