<?php
/**
 * Bootstrap for PHPStan
 *
 * Defines the path constants and loads the CakePHP core the same way
 * webroot/index.php does, without dispatching a request or connecting
 * to a database.
 */

use Composer\InstalledVersions;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once dirname(__DIR__) . DS . 'config' . DS . 'define.php';
require_once VENDORS . 'autoload.php';
require_once InstalledVersions::getInstallPath('pieceofcake2/cakephp') . DS . 'src' . DS . 'bootstrap.php';
