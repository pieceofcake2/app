<?php
/**
 * Bootstrap for PHPUnit
 *
 * The tests always run with APP_ENV=test, so config/core.php loads config/.env.test, where
 * DATABASE_URL is the test database. It is set here, so it also applies when PHPUnit runs
 * without phpunit.xml.dist. The web server of the End-to-End tests inherits it.
 *
 * bin/cake test includes this file after config/core.php has loaded the .env files, too late
 * to select config/.env.test: run APP_ENV=test bin/cake test ... instead.
 */

use Composer\InstalledVersions;

if (class_exists('Configure', false)) {
    // Included by bin/cake test: the .env files are already loaded.
    if (Configure::read('App.env') !== 'test') {
        throw new RuntimeException(
            'bin/cake test loaded the .env files without APP_ENV=test. ' .
            'Run APP_ENV=test bin/cake test ... or vendor/bin/phpunit.',
        );
    }
} else {
    putenv('APP_ENV=test');
    $_ENV['APP_ENV'] = 'test';
    $_SERVER['APP_ENV'] = 'test';
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}
if (!defined('VENDORS')) {
    define('VENDORS', ROOT . DS . 'vendor' . DS);
}

require_once InstalledVersions::getInstallPath('pieceofcake2/cakephp') . DS . 'tests' . DS . 'bootstrap.php';
