<?php
/**
 * Default home page, shown while debug mode is enabled.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://pieceofcake2.github.io/docs/ CakePHP 2 documentation
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var View $this
 */

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\CakePlugin;
use Cake\Core\Configure;
use Cake\Error\NotFoundException;
use Cake\Model\ConnectionManager;
use Cake\Utility\Validation;
use Cake\View\View;

if (!Configure::read('debug')):
    throw new NotFoundException();
endif;

// This page brings its own HTML document instead of using a layout.
$this->layout = false;

$docsUrl = 'https://pieceofcake2.github.io/docs/';

/**
 * Renders a list item with a success or problem bullet.
 *
 * @param bool $ok Whether the check passed
 * @param string $success HTML shown when the check passed
 * @param string $problem HTML shown when the check failed
 * @return string
 */
$bullet = function ($ok, $success, $problem) {
    return sprintf('<li class="bullet %s">%s</li>', $ok ? 'success' : 'problem', $ok ? $success : $problem);
};

// Database
$databaseConfigPresent = file_exists(CONFIG . 'database.php');
$connected = false;
$connectionError = '';
$datasource = null;
if ($databaseConfigPresent):
    try {
        $connected = ConnectionManager::getDataSource('default')->isConnected();
    } catch (Exception $exception) {
        $connectionError = h($exception->getMessage());
        if (method_exists($exception, 'getAttributes')):
            $attributes = $exception->getAttributes();
            if (isset($attributes['message']) && !str_contains($exception->getMessage(), (string)$attributes['message'])):
                $connectionError .= '<br />' . h($attributes['message']);
            endif;
        endif;
    }
    $connections = ConnectionManager::enumConnectionObjects();
    if (isset($connections['default']['datasource'])):
        $datasource = $connections['default']['datasource'];
    endif;
endif;

// PDO driver of the default connection
$pdoDrivers = class_exists('PDO') ? PDO::getAvailableDrivers() : [];
$driverNames = ['Mysql' => 'mysql', 'Postgres' => 'pgsql', 'Sqlite' => 'sqlite', 'Sqlserver' => 'sqlsrv'];
$driver = null;
if ($datasource !== null):
    $datasourceClass = substr((string)strrchr('/' . $datasource, '/'), 1);
    if (isset($driverNames[$datasourceClass])):
        $driver = $driverNames[$datasourceClass];
    endif;
endif;

// Cache
$coreCache = Cache::settings('_cake_core_');
$modelCache = Cache::settings('_cake_model_');
$defaultCache = Cache::settings('default');
$cacheWorks = false;
if (!empty($defaultCache)):
    $cacheKey = 'home_page_check';
    $cacheWorks = Cache::write($cacheKey, 'ok', 'default') && Cache::read($cacheKey, 'default') === 'ok';
    Cache::delete($cacheKey, 'default');
endif;

// Development plugins
$debugKitInstalled = false;
foreach (App::path('plugins') as $pluginPath):
    if (is_dir($pluginPath . 'DebugKit')):
        $debugKitInstalled = true;
    endif;
endforeach;
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo __d('cake_dev', 'CakePHP: the rapid development php framework'); ?>:
        <?php echo $this->fetch('title'); ?>
    </title>
    <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake', 'home']);

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>
<body>
    <header>
        <div class="container text-center">
            <a href="https://cakephp.org/" target="_blank" rel="noopener">
                <?php echo $this->Html->image('cake.logo.svg', ['alt' => 'CakePHP', 'width' => 350, 'class' => 'cake-logo']); ?>
            </a>
            <h1>
                <?php echo __d('cake_dev', 'Welcome to CakePHP %s', h(Configure::version())); ?>
            </h1>
            <p class="subtitle">
                <?php echo __d('cake_dev', 'pieceofcake2/cakephp, the community-maintained CakePHP 2 for PHP 8.'); ?>
                <a target="_blank" rel="noopener" href="https://github.com/pieceofcake2/cakephp/releases/tag/v<?php echo h(Configure::version()); ?>"><?php echo __d('cake_dev', 'Read the changelog'); ?></a>
            </p>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <div class="content">
                <div class="row">
                    <div class="column">
                        <div class="message default text-center">
                            <small><?php echo __d('cake_dev', 'Please be aware that this page will not be shown if you turn off debug mode unless you replace %s with your own version.', 'templates/Pages/home.php'); ?></small>
                        </div>
                        <div id="url-rewriting-warning" class="message error">
                            <ul>
                                <li class="bullet problem">
                                    <?php echo __d('cake_dev', 'URL rewriting is not properly configured on your server.'); ?><br />
                                    1) <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>installation/url-rewriting.html"><?php echo __d('cake_dev', 'Help me configure it'); ?></a><br />
                                    2) <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>installation/url-rewriting.html#i-don-t-can-t-use-url-rewriting"><?php echo __d('cake_dev', 'I don\'t / can\'t use URL rewriting'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="column">
                        <h4><?php echo __d('cake_dev', 'Environment'); ?></h4>
                        <ul>
                            <?php
                            echo $bullet(
                                PHP_VERSION_ID >= 80000,
                                __d('cake_dev', 'Your version of PHP is 8.0 or higher (detected %s).', PHP_VERSION),
                                __d('cake_dev', 'Your version of PHP is too low. You need PHP 8.0 or higher to use CakePHP (detected %s).', PHP_VERSION),
                            );
                            foreach (['mbstring', 'intl', 'openssl'] as $extension):
                                echo $bullet(
                                    extension_loaded($extension),
                                    __d('cake_dev', 'Your version of PHP has the %s extension loaded.', $extension),
                                    __d('cake_dev', 'Your version of PHP does NOT have the %s extension loaded. It is recommended; CakePHP falls back to a slower polyfill or cannot use some features without it.', $extension),
                                );
                            endforeach;
                            echo $bullet(
                                class_exists('PDO'),
                                __d('cake_dev', 'Your version of PHP has the PDO extension loaded (drivers: %s).', h(implode(', ', $pdoDrivers) ?: __d('cake_dev', 'none'))),
                                __d('cake_dev', 'Your version of PHP does NOT have the PDO extension loaded. CakePHP needs it to connect to a database.'),
                            );
                            if ($driver !== null):
                                echo $bullet(
                                    in_array($driver, $pdoDrivers, true),
                                    __d('cake_dev', 'The %s extension needed by %s is loaded.', '<code>pdo_' . $driver . '</code>', '<code>' . h($datasource) . '</code>'),
                                    __d('cake_dev', 'The %s extension needed by %s is NOT loaded.', '<code>pdo_' . $driver . '</code>', '<code>' . h($datasource) . '</code>'),
                                );
                            endif;
                            if (!Validation::alphaNumeric('cakephp')):
                                echo $bullet(
                                    false,
                                    '',
                                    __d('cake_dev', 'PCRE has not been compiled with Unicode support.') . '<br />'
                                    . __d('cake_dev', 'Recompile PCRE with Unicode support by adding <code>--enable-unicode-properties</code> when configuring'),
                                );
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div class="column">
                        <h4><?php echo __d('cake_dev', 'Filesystem'); ?></h4>
                        <ul>
                            <?php
                            echo $bullet(
                                is_writable(TMP),
                                __d('cake_dev', 'Your tmp directory is writable.'),
                                __d('cake_dev', 'Your tmp directory is NOT writable.'),
                            );
                            echo $bullet(
                                is_writable(LOGS),
                                __d('cake_dev', 'Your logs directory is writable.'),
                                __d('cake_dev', 'Your logs directory is NOT writable.'),
                            );
                            echo $bullet(
                                $cacheWorks,
                                __d('cake_dev', 'Your cache is working (the %s engine of the default cache config).', '<em>' . h($defaultCache['engine'] ?? '') . '</em>'),
                                __d('cake_dev', 'Your cache is NOT working. Please check the settings in %s', '<code>config/bootstrap.php</code>'),
                            );
                            echo $bullet(
                                !empty($coreCache),
                                __d('cake_dev', 'The %s engine is being used for core caching. To change the config edit %s', '<em>' . h($coreCache['engine'] ?? '') . '</em>', '<code>config/core.php</code>'),
                                __d('cake_dev', 'Your core cache is NOT configured. Please check the settings in %s', '<code>config/core.php</code>'),
                            );
                            ?>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="column">
                        <h4><?php echo __d('cake_dev', 'Database'); ?></h4>
                        <ul>
                            <?php
                            echo $bullet(
                                $databaseConfigPresent,
                                __d('cake_dev', 'Your database configuration file is present.'),
                                __d('cake_dev', 'Your database configuration file is NOT present.') . '<br />'
                                . __d('cake_dev', 'Copy %s to %s', '<code>config/database.php.default</code>', '<code>config/database.php</code>'),
                            );
                            if ($databaseConfigPresent):
                                echo $bullet(
                                    $connected,
                                    __d('cake_dev', 'CakePHP is able to connect to the database.'),
                                    __d('cake_dev', 'CakePHP is NOT able to connect to the database.')
                                    . ($connectionError !== '' ? '<br />' . $connectionError : ''),
                                );
                                echo $bullet(
                                    !empty($modelCache),
                                    __d('cake_dev', 'The %s engine is being used for database schema caching. To change the config edit %s', '<em>' . h($modelCache['engine'] ?? '') . '</em>', '<code>config/core.php</code>'),
                                    __d('cake_dev', 'Your database schema cache is NOT configured. Please check the settings in %s', '<code>config/core.php</code>'),
                                );
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div class="column">
                        <h4><?php echo __d('cake_dev', 'Configuration'); ?></h4>
                        <ul>
                            <?php
                            echo $bullet(
                                Configure::read('Security.salt') !== 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi',
                                __d('cake_dev', '%s is set to a value specific to your application.', '<code>Security.salt</code>'),
                                __d('cake_dev', 'Please change the value of %s to a salt value specific to your application. Set %s in %s or edit %s.', '<code>Security.salt</code>', '<code>SECURITY_SALT</code>', '<code>config/.env.local</code>', '<code>config/core.php</code>'),
                            );
                            echo $bullet(
                                Configure::read('Security.cipherSeed') !== '76859309657453542496749683645',
                                __d('cake_dev', '%s is set to a value specific to your application.', '<code>Security.cipherSeed</code>'),
                                __d('cake_dev', 'Please change the value of %s to a numeric (digits only) seed value specific to your application. Set %s in %s or edit %s.', '<code>Security.cipherSeed</code>', '<code>SECURITY_CIPHER_SEED</code>', '<code>config/.env.local</code>', '<code>config/core.php</code>'),
                            );
                            ?>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="column">
                        <h4>DebugKit</h4>
                        <ul>
                            <?php
                            if (CakePlugin::loaded('DebugKit') && isset($this->helpers['DebugKit.Toolbar'])):
                                echo $bullet(true, __d('cake_dev', 'DebugKit is loaded and its toolbar is enabled.'), '');
                            elseif (CakePlugin::loaded('DebugKit')):
                                echo $bullet(true, __d('cake_dev', 'DebugKit is loaded. It will help you inspect and debug different aspects of your application.'), '');
                                echo $bullet(
                                    false,
                                    '',
                                    __d('cake_dev', 'Add the %s component to your AppController to show the toolbar.', '<code>DebugKit.Toolbar</code>'),
                                );
                            elseif ($debugKitInstalled):
                                echo $bullet(
                                    false,
                                    '',
                                    __d('cake_dev', 'DebugKit is installed but not loaded. It will help you inspect and debug different aspects of your application.') . '<br />'
                                    . __d('cake_dev', 'Load it with %s in %s and add the %s component to your AppController.', '<code>CakePlugin::load(\'DebugKit\');</code>', '<code>config/bootstrap.php</code>', '<code>DebugKit.Toolbar</code>'),
                                );
                            else:
                                echo $bullet(
                                    false,
                                    '',
                                    __d('cake_dev', 'DebugKit is not installed. It will help you inspect and debug different aspects of your application.') . '<br />'
                                    . __d('cake_dev', 'Install it with %s. See %s for details.', '<code>composer require --dev pieceofcake2/debug_kit</code>', $this->Html->link('pieceofcake2/debug_kit', 'https://pieceofcake2.github.io/debug_kit/', ['target' => '_blank', 'rel' => 'noopener'])),
                                );
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div class="column">
                        <h4>Bake</h4>
                        <ul>
                            <?php
                            echo $bullet(
                                CakePlugin::loaded('Bake'),
                                __d('cake_dev', 'Bake is loaded. Generate models, controllers and views with %s.', '<code>bin/cake bake</code>'),
                                __d('cake_dev', 'Bake is not loaded. Install it with %s to generate code from your database schema.', '<code>composer require --dev pieceofcake2/bake</code>'),
                            );
                            ?>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="column">
                        <h3><?php echo __d('cake_dev', 'Editing this Page'); ?></h3>
                        <ul class="plain">
                            <li><?php echo __d('cake_dev', 'To change the content of this page, edit: %s.', '<code>templates/Pages/home.php</code>'); ?></li>
                            <li><?php echo __d('cake_dev', 'To change its layout, edit: %s.', '<code>templates/layout/default.php</code>'); ?></li>
                            <li><?php echo __d('cake_dev', 'You can also add some CSS styles for your pages at: %s.', '<code>webroot/css</code>'); ?></li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="column links">
                        <h3><?php echo __d('cake_dev', 'Getting Started'); ?></h3>
                        <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>"><?php echo __d('cake_dev', 'CakePHP 2 Documentation'); ?></a>
                        <a target="_blank" rel="noopener" href="https://pieceofcake2.github.io/app/"><?php echo __d('cake_dev', 'Application Skeleton'); ?></a>
                        <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>installation.html"><?php echo __d('cake_dev', 'Installation'); ?></a>
                        <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>development/configuration.html"><?php echo __d('cake_dev', 'Configuration'); ?></a>
                        <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>tutorials-and-examples/blog/blog.html"><?php echo __d('cake_dev', 'Blog Tutorial'); ?></a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="column links">
                        <h3><?php echo __d('cake_dev', 'Plugins'); ?></h3>
                        <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>plugins/maintained-plugins.html"><?php echo __d('cake_dev', 'Maintained Plugins'); ?></a>
                        <a target="_blank" rel="noopener" href="https://pieceofcake2.github.io/debug_kit/">pieceofcake2/debug_kit</a>
                        <a target="_blank" rel="noopener" href="https://pieceofcake2.github.io/bake/">pieceofcake2/bake</a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="column links">
                        <h3><?php echo __d('cake_dev', 'Help and Bug Reports'); ?></h3>
                        <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>cakephp-overview/where-to-get-help.html"><?php echo __d('cake_dev', 'Where to Get Help'); ?></a>
                        <a target="_blank" rel="noopener" href="https://github.com/pieceofcake2/cakephp/issues"><?php echo __d('cake_dev', 'CakePHP Issues'); ?></a>
                        <a target="_blank" rel="noopener" href="<?php echo $docsUrl; ?>appendices/security-fixes.html"><?php echo __d('cake_dev', 'Security Fixes'); ?></a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="column links">
                        <h3><?php echo __d('cake_dev', 'About pieceofcake2/cakephp'); ?></h3>
                        <p>
                            <?php echo __d('cake_dev', 'pieceofcake2/cakephp is a community-maintained fork of CakePHP 2.x that runs on PHP 8. It is meant for maintaining existing CakePHP 2 applications; for new projects, use the latest CakePHP release.'); ?>
                        </p>
                        <a target="_blank" rel="noopener" href="https://github.com/pieceofcake2"><?php echo __d('cake_dev', 'pieceofcake2 on GitHub'); ?></a>
                        <a target="_blank" rel="noopener" href="https://cakephp.org"><?php echo __d('cake_dev', 'CakePHP'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
