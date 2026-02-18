<?php

namespace GainTest;

use NanoSoup\Zeus\Kernel as KernelBase;
use GainTest\ACF\ACF;
use GainTest\WordPress\Images;
use GainTest\WordPress\Twig;

class Kernel extends KernelBase
{
    public function __construct()
    {
        parent::__construct();
        $this->registerClasses();
    }

    /**
     * @return array
     */
    public function registerClasses()
    {
        return [
            new ACF(),
            new Twig(),
            new Images(),
        ];
    }

    public static function getAction(string $route): string
    {
        return lcfirst(str_replace('-', '', ucwords($route, '-'))) . 'Action';
    }
}
