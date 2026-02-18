<?php

namespace Mimir;

use Composer\Script\Event;
use Composer\IO\IOInterface;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class Wordpress
 * @package Mimir
 */
class Wordpress
{
    private static $vendor_dir = '';
    private static $site_name = '';
    private static $site_url = '';
    private static $admin_username = '';
    private static $admin_password = '';
    private static $theme_package = 'eleven-miles/erebus';

    public static function install(Event $event)
    {
        self::$vendor_dir = $event->getComposer()->getConfig()->get('vendor-dir');
        require self::$vendor_dir . '/autoload.php';

        $io = $event->getIO();

        self::coreInstall($io);
        self::themeInstall($io, $event);
        self::pluginInstall($io);
        self::gitInstall($io);
        self::stepCompletion(
            $io,
            'Mimir has finished setting up your WordPress project! :)',
            self::$site_url,
            self::$admin_username,
            self::$admin_password
        );
    }

    /**
     * Sets up the basic WP environment variables and core install
     *
     * @param Event $event
     */
    private static function coreInstall($io)
    {
        $default_rand_pass = substr(
            str_shuffle(
                str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+', 16)
            ),
            0,
            16
        );

        try {
            self::stepTitle($io, 'Setting up WordPress environment variables & salts');

            $env = new Dotenv();
            $env->load(self::$vendor_dir . '/../.env.example');
            $example_env_vars = explode(',', $_ENV['SYMFONY_DOTENV_VARS']);
            $file_contents = '';

            foreach ($example_env_vars as $var) {
                $var_name = $var;

                if ($_ENV[$var] !== '') {
                    $var_name .= " ({$_ENV[$var]})";
                }

                $var_question_response = $io->ask("$var_name: ", $_ENV[$var]);
                $file_contents .= $var . '="' . $var_question_response . '"' . PHP_EOL;
            }

            file_put_contents(self::$vendor_dir . '/../.env', $file_contents);
        } catch (\Exception $e) {
            $io->writeError('Error setting up WordPress Environment variables: ' . $e->getMessage());
        }

        try {
            $file_contents = '';
            $wp_salts = file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/');

            /**
             * Get the actual values we need
             */
            $re = '/define\(\'(.*)?\',.*?\'(.*)?\'.*/m';
            preg_match_all($re, $wp_salts, $matches, PREG_PATTERN_ORDER, 0);

            for ($i = 0; $i < count($matches[1]); $i++) {
                $file_contents .= $matches[1][$i] . '="' . $matches[2][$i] . '"' . PHP_EOL;
            }

            file_put_contents(self::$vendor_dir . '/../.env', $file_contents, FILE_APPEND);
        } catch (\Exception $e) {
            $io->writeError('Error setting up WordPress salts: ' . $e->getMessage());
        }

        self::stepLoader($io);

        try {
            self::stepTitle($io, 'Setting up WordPress install');

            $site_name = $io->ask('What is the name of the site? (WordPress Site): ', 'WordPress Site');
            $site_url = $io->ask('What is the URL of the site?: ');
            $admin_email = $io->ask('What is the admin email? (dev@elevenmiles.com): ', 'dev@elevenmiles.com');
            $admin_username = $io->ask('What is the admin username? (super.user): ', 'super.user');
            $admin_password = $io->ask("What is the admin password? ($default_rand_pass): ", $default_rand_pass);

            self::$site_name = $site_name;
            self::$site_url = $site_url;
            self::$admin_username = $admin_username;
            self::$admin_password = $admin_password;

            $io->write('');

            $wp_bin_path = self::$vendor_dir . '/bin/wp';

            system("$wp_bin_path core download --locale='en_GB' --skip-content");
            system("$wp_bin_path db create");
            system("$wp_bin_path core install --locale='en_GB' --url='$site_url' --title='$site_name' --admin_user='$admin_username' --admin_email='$admin_email' --admin_password='$admin_password' --skip-email");

            self::stepLoader($io);
        } catch (\Exception $e) {
            $io->writeError('Error setting up WordPress: ' . $e->getMessage());
        }
    }

    /**
     * Sets up the WP theme install
     * @param IOInterface $io
     * @param Event $event
     */
    private static function themeInstall(IOInterface $io, Event $event)
    {
        $vendor_dir = self::$vendor_dir;

        try {
            self::stepTitle($io, 'Setting up WordPress theme');

            $theme_name = $io->askAndValidate('What is the name of the theme (lowercase & hyphenated)?: ', function ($theme_name) {
                if (empty($theme_name)) {
                    throw new \Exception('The theme name cannot be empty');
                }

                if (!preg_match('/^[a-z0-9-]+$/', $theme_name)) {
                    throw new \Exception('The theme name must be lowercase and hyphenated');
                }

                return $theme_name;
            });

            $theme_package = $io->ask('What is the package name of the theme? (' . self::$theme_package . '): ', self::$theme_package);
            $theme_namespace = $io->ask('What would you like the Namespace within the theme to be? (ElevenMiles): ', 'ElevenMiles');

            system("cd wp-content/themes && composer create-project $theme_package $theme_name && cd $vendor_dir/../");

            // Update theme namespacing using the namespace-replacements array within
            // the theme.config.json file
            if (file_exists(self::$vendor_dir . '/../scripts/theme.config.json')) {
                $theme_config = json_decode(
                    file_get_contents(self::$vendor_dir . '/../scripts/theme.config.json'),
                    true
                );

                if (!empty($theme_config) && !empty($theme_config['namespace-replacements'])) {
                    foreach ($theme_config['namespace-replacements'] as $theme_filename) {
                        $theme_file_path = "wp-content/themes/$theme_name/$theme_filename";
                        $theme_file = file_get_contents($theme_file_path);

                        file_put_contents(
                            $theme_file_path,
                            str_replace('{{ SITE_NAMESPACE }}', $theme_namespace, $theme_file)
                        );
                    }
                }
            }

            // Fetch current composer autoload and add the theme namespace
            $package_autoload = $event->getComposer()->getPackage()->getAutoload();
            $package_autoload['psr-4']["$theme_namespace\\"] = [
                "wp-content/themes/$theme_name/classes",
                "wp-content/themes/$theme_name/controllers"
            ];

            $composerJsonPath = "$vendor_dir/../composer.json";
            $composerConfig = json_decode(file_get_contents($composerJsonPath), true);

            // Update autoload section in composer.json
            $composerConfig['autoload'] = $package_autoload;

            file_put_contents(
                $composerJsonPath,
                json_encode($composerConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            // Run composer dump-autoload to update the autoloader
            system('composer dump-autoload');

            $wp_bin_path = "$vendor_dir/bin/wp";
            system("$wp_bin_path theme activate $theme_name");

            self::stepLoader($io);
        } catch (\Exception $e) {
            $io->writeError('Error setting up WordPress theme: ' . $e->getMessage());
        }
    }

    /**
     * Sets up the WP plugin install
     *
     * @param IOInterface $io
     */
    private static function pluginInstall(IOInterface $io)
    {
        // TODO: add plugin install from default list
        // try {
        //     self::stepTitle($io, 'Setting up WordPress plugins');
        //     system("wp plugin activate --all");
        //     self::stepLoader($io);
        // } catch (\Exception $e) {
        //     $io->writeError('Error setting up WordPress Plugins: ' . $e->getMessage());
        // }
    }

    /**
     * Sets up the project's git repo install/setup
     *
     * @param IOInterface $io
     */
    private static function gitInstall(IOInterface $io)
    {
        try {
            // TODO: Add the ability to add a git repo to the project
            $project_git_url = $io->ask('Git repo URL?: ');
            $site_name = self::$site_name;

            system("git init && git remote add origin $project_git_url");
            system("git add . && git commit -m 'Initial project commit for $site_name' && git push -u origin master");
        } catch (\Exception $e) {
            $io->writeError('Error setting up Git Repository: ' . $e->getMessage());
        }
    }

    private static function stepTitle($io, $title = '', $dividerChar = '=')
    {
        $divider = str_repeat($dividerChar, strlen($title));
        $io->write($divider);
        $io->write($title);
        $io->write($divider);
    }

    private static function stepLoader($io, $counter = 6, $delay = 250_000)
    {
        $io->write('');

        for ($i = 0; $i < $counter; $i++) {
            $newline = $i === $counter - 1 ? true : false;
            $io->write('.', $newline);
            usleep($delay);
        }

        $io->write('');
    }

    private static function stepCompletion($io, $title = '', $site_url = '', $username = '', $password = '', $dividerChar = '=')
    {
        $divider = str_repeat($dividerChar, strlen($title));
        $io->write($divider);
        $io->write($title);
        $io->write($divider);
        $io->write('Your site is now available at: ' . $site_url);
        $io->write('Your admin username is: ' . $username);
        $io->write('Your admin password is: ' . $password);
        $io->write($divider);
    }
}
