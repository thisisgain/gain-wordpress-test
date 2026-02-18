<?php

namespace GainTest\WordPress;

use Timber\Timber;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;
use Timber\Site;

class Twig
{
    /**
     * Twig constructor.
     */
    public function __construct()
    {
        add_action('init', [$this, 'additionalTwigFileLoaderPaths']);
        add_filter('timber/twig', [$this, 'registerTwigFunctions']);
        add_filter('timber_context', [$this, 'addToContext']);
    }

    /**
     * This adds stuff to the global context - note this is run on EVERY page
     * so only put in what is really needed AND can't be done via the controller
     *
     * @param $data
     * @return mixed
     */
    public function addToContext($data)
    {
        if (!session_id()) {
            session_start();
        }

        $loader = new FilesystemLoader(get_template_directory());
        $twig = new Environment($loader);
        $twig->addGlobal('_session', $_SESSION);
        $twig->addGlobal('_post', $_POST);
        $twig->addGlobal('_get', $_GET);

        $data['site'] = new Site();

        return $data;
    }

    /**
     * Registers additional paths in Timber
     */
    public function additionalTwigFileLoaderPaths()
    {
        Timber::$locations = [
            get_template_directory() . '/public/dist/',
        ];
    }

    /**
     * @param Environment $twig
     * @return Environment
     */
    public function registerTwigFunctions(Environment $twig)
    {
        $twig->addFunction(new TwigFunction('getSanitiseTitleWithDashes', [__CLASS__, 'getSanitiseTitleWithDashes']));
        return $twig;
    }

    /**
     * @param $string
     * @return $string
     */
    public static function getSanitiseTitleWithDashes($label)
    {
        return sanitize_title_with_dashes($label);
    }
}
