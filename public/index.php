<?php

use Peppers\Kernel;

abstract class Settings {

    private static bool $_booted = false;
    private static bool $_protected = false;

    /**
     * 
     * @return string
     */
    public static function getRuntimeEnvironment(): string {
        return static::$_settings[static::$_runtimeEnvironment];
    }

    /**
     * 
     * @return bool
     */
    public static function appInProduction(): bool {
        return static::getRuntimeEnvironment() == static::$_settings['RUN_PRD'];
    }

    /**
     * 
     * @return void
     */
    public static function protect(): void {
        static::$_protected = true;
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    public static function get(string $name): mixed {
        return array_key_exists($name, static::$_settings) 
                ? static::$_settings[$name] 
                : null;
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public static function set(
            string $name,
            mixed $value
    ): mixed {
        if (static::$_protected || !array_key_exists($name, static::$_settings)) {
            return null;
        }

        return static::$_settings[$name] = $value;
    }

    private static string $_runtimeEnvironment = 'RUN_DEV';

    /**
     * 
     * @var array
     */
    private static array $_settings = [
        /* Use this array to set global configuration switches/values */
        'RUN_DEV' => 'development',
        'RUN_PRD' => 'production',
        'RUN_STG' => 'staging',
        'MAX_URL_QUERIES' => '128',
        'MIN_URL_QUERIES' => '0',
        'MODEL_PRIMARY_KEY_DEFAULT_REGEX' => '[0-9a-zA-Z\s\.\-\@\_]+',
        'LOG_KERNEL_FLOW' => false,
        'KERNEL_LOG_FILE' => 'log-kernel',
        'KERNEL_PANIC_FILE' => 'panic-kernel',
        'KERNEL_PANIC_MESSAGE' => 'Something is wrong with Peppers! Sorry we cannot help now (... this was logged though).',
        'ROUTES_CACHE_FILENAME' => 'routescache',
        'WEBROOT' => __DIR__ . DIRECTORY_SEPARATOR,
        /* value for this constant corresponds to a directory inside 
         * PEPPERS_APP_VIEW_DIR directory:
         * directory.another_directory.(...).view_file.start_section_inside_view_file
         */
        'UNCAUGHT_EXCEPTION_DEFAULT_VIEW' => 'Peppers.DefaultException.defaultSection',
        'HTTP_ALLOW_FREE_QUERY' => true,
        'HTTP_DEFAULT_MIME_TYPE' => '*/*',
        'REGEX_DEFAULT_ROUTE_PARAMETER' => '[a-z0-9]+',
        'REGEX_DEFAULT_HTTP_ACCEPT_HEADER' => '/[a-z\/,+\*]+(;q=\d\.\d,?)?/i',
        'USE_DEFAULT_CONTROLLER_ON_404' => true,
    ];

    /**
     * 
     * @return void
     */
    public static function boot(): void {
        if (static::$_booted) {
            return;
        }

        $DS = DIRECTORY_SEPARATOR;
        static::$_settings['DOCROOT'] = static::$_settings['WEBROOT'] . '..' . $DS;
        static::$_settings['PRIVATE_DIR'] = static::$_settings['DOCROOT'] . 'private' . $DS;
        static::$_settings['LOGS_DIR'] = static::$_settings['DOCROOT'] . 'logs' . $DS;
        static::$_settings['APP_DIR'] = static::$_settings['DOCROOT'] . 'App' . $DS;
        static::$_settings['APP_VIEW_DIR'] = static::$_settings['APP_DIR'] . 'Views' . $DS;
        static::$_settings['APP_CONFIG_DIR'] = static::$_settings['DOCROOT'] . 'config' . $DS;
        static::$_settings['TEMP_DIR'] = static::$_settings['DOCROOT'] . 'temp' . $DS;
        static::$_settings['REGEX_DEFAULT_ROUTE_FREE_QUERY'] = sprintf(
                '(?:(?:&|\?)[^=&]+=[^=&]+?){%s,%s}',
                static::$_settings['MIN_URL_QUERIES'],
                static::$_settings['MAX_URL_QUERIES']
        );
        static::$_booted = true;
    }

}

Settings::boot();
if (Settings::appInProduction()) {
    ini_set('display_errors', 'off');
    error_reporting(0);
} else {
    Settings::set('LOG_KERNEL_FLOW', true);
    ini_set('display_errors', 'on');
    error_reporting(E_ALL);
}

Settings::protect();
mb_internal_encoding('UTF-8');
ob_start('mb_output_handler');

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

Kernel::go();
