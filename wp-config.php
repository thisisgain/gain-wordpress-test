<?php

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */

$table_prefix = 'wp_';

/* Add any custom values between this line and the "stop editing" line. */

use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

$env = new Dotenv();
$env->load(__DIR__ . '/.env');

foreach (explode(',', $_ENV['SYMFONY_DOTENV_VARS']) as $env_var) {
    define(
        $env_var,
        filter_var(
            $_ENV[$env_var],
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        ) ?? $_ENV[$env_var]
    );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
