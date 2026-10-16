<?php
/**
 * Bootstrap for PHPUnit
 *
 * The tests run with APP_ENV=test by default (an APP_ENV from the environment wins), so
 * config/core.php loads config/.env.test, where DATABASE_URL is the test database. It is set
 * here, so it also applies when PHPUnit runs without phpunit.xml.dist, and the web server of the
 * End-to-End tests inherits it. bin/cake test sets the same default before it loads the .env files.
 */

use Composer\InstalledVersions;

if ((string)getenv('APP_ENV') === '' && empty($_SERVER['APP_ENV'])) {
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
