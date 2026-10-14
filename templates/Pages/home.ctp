<?php
/**
 * Default home page, shown while debug mode is enabled.
 *
 * @link          https://pieceofcake2.github.io/docs/ Piece of Cake 2 documentation
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

if (!Configure::read('debug')):
    throw new NotFoundException();
endif;

$docsUrl = 'https://pieceofcake2.github.io/docs/';
?>
<h2><?php echo __d('cake_dev', 'Release Notes for CakePHP %s.', h(Configure::version())); ?></h2>
<p>
    <?php echo $this->Html->link(__d('cake_dev', 'Read the changelog'), 'https://github.com/pieceofcake2/cakephp/releases/tag/v' . Configure::version()); ?>
</p>
<?php
Debugger::checkSecurityKeys();
?>
<?php if (file_exists(WWW_ROOT . 'css' . DS . 'cake.generic.css')): ?>
    <p id="url-rewriting-warning" style="background-color:#e32; color:#fff;">
        <?php echo __d('cake_dev', 'URL rewriting is not properly configured on your server.'); ?>
        1) <a target="_blank" href="<?php echo $docsUrl; ?>installation/url-rewriting.html" style="color:#fff;"><?php echo __d('cake_dev', 'Help me configure it'); ?></a>
        2) <a target="_blank" href="<?php echo $docsUrl; ?>installation/url-rewriting.html#i-don-t-can-t-use-url-rewriting" style="color:#fff;"><?php echo __d('cake_dev', 'I don\'t / can\'t use URL rewriting'); ?></a>
    </p>
<?php endif; ?>
<p>
    <?php
    if (PHP_VERSION_ID >= 80000):
        echo '<span class="notice success">';
            echo __d('cake_dev', 'Your version of PHP (%s) is 8.0 or higher.', PHP_VERSION);
        echo '</span>';
    else:
        echo '<span class="notice">';
            echo __d('cake_dev', 'Your version of PHP (%s) is too old. CakePHP requires PHP 8.0 or higher.', PHP_VERSION);
        echo '</span>';
    endif;
    ?>
</p>
<p>
    <?php
    if (is_writable(TMP)):
        echo '<span class="notice success">';
            echo __d('cake_dev', 'Your tmp directory is writable.');
        echo '</span>';
    else:
        echo '<span class="notice">';
            echo __d('cake_dev', 'Your tmp directory is NOT writable.');
        echo '</span>';
    endif;
    ?>
</p>
<p>
    <?php
    $settings = Cache::settings();
    if (!empty($settings)):
        echo '<span class="notice success">';
            echo __d('cake_dev', 'The %s is being used for core caching. To change the config edit %s', '<em>' . h($settings['engine']) . 'Engine</em>', 'config/core.php');
        echo '</span>';
    else:
        echo '<span class="notice">';
            echo __d('cake_dev', 'Your cache is NOT working. Please check the settings in %s', 'config/core.php');
        echo '</span>';
    endif;
    ?>
</p>
<?php if (!file_exists(CONFIG . '.env') && file_exists(CONFIG . '.env.example')): ?>
    <p>
        <span class="notice">
            <?php echo __d('cake_dev', 'Your %s file is NOT present.', 'config/.env'); ?>
            <br/>
            <?php echo __d('cake_dev', 'To set environment variables (debug level, security keys, database) for local development, copy %s to %s.', 'config/.env.example', 'config/.env'); ?>
        </span>
    </p>
<?php endif; ?>
<p>
    <?php
    $filePresent = file_exists(CONFIG . 'database.php');
    if ($filePresent):
        echo '<span class="notice success">';
            echo __d('cake_dev', 'Your database configuration file is present.');
        echo '</span>';
    else:
        echo '<span class="notice">';
            echo __d('cake_dev', 'Your database configuration file is NOT present.');
            echo '<br/>';
            echo __d('cake_dev', 'Copy %s to %s', 'config/database.php.default', 'config/database.php');
        echo '</span>';
    endif;
    ?>
</p>
<?php
if ($filePresent):
    $connected = false;
    $errorMsg = '';
    try {
        $connected = ConnectionManager::getDataSource('default')->isConnected();
    } catch (Exception $connectionError) {
        $errorMsg = h($connectionError->getMessage());
        if (method_exists($connectionError, 'getAttributes')):
            $attributes = $connectionError->getAttributes();
            if (isset($attributes['message'])):
                $errorMsg .= '<br />' . h($attributes['message']);
            endif;
        endif;
    }
    ?>
    <p>
        <?php
        if ($connected):
            echo '<span class="notice success">';
                echo __d('cake_dev', 'CakePHP is able to connect to the database.');
            echo '</span>';
        else:
            echo '<span class="notice">';
                echo __d('cake_dev', 'CakePHP is NOT able to connect to the database.');
                if ($errorMsg !== ''):
                    echo '<br /><br />';
                    echo $errorMsg;
                endif;
            echo '</span>';
        endif;
        ?>
    </p>
    <?php
endif;

if (!Validation::alphaNumeric('cakephp')):
    echo '<p><span class="notice">';
        echo __d('cake_dev', 'PCRE has not been compiled with Unicode support.');
        echo '<br/>';
        echo __d('cake_dev', 'Recompile PCRE with Unicode support by adding <code>--enable-unicode-properties</code> when configuring');
    echo '</span></p>';
endif;
?>
<p>
    <?php
    $debugKitInstalled = false;
    foreach (App::path('plugins') as $pluginPath):
        if (is_dir($pluginPath . 'DebugKit')):
            $debugKitInstalled = true;
        endif;
    endforeach;

    if (CakePlugin::loaded('DebugKit')):
        echo '<span class="notice success">';
            echo __d('cake_dev', 'DebugKit plugin is present');
        echo '</span>';
    elseif ($debugKitInstalled):
        echo '<span class="notice">';
            echo __d('cake_dev', 'DebugKit is installed but not loaded. It will help you inspect and debug different aspects of your application.');
            echo '<br/>';
            echo __d('cake_dev', 'Load it with %s in %s and add the %s component to your AppController.', '<code>CakePlugin::load(\'DebugKit\');</code>', 'config/bootstrap.php', '<code>DebugKit.Toolbar</code>');
        echo '</span>';
    else:
        echo '<span class="notice">';
            echo __d('cake_dev', 'DebugKit is not installed. It will help you inspect and debug different aspects of your application.');
            echo '<br/>';
            echo __d('cake_dev', 'Install it with %s. See %s for details.', '<code>composer require --dev pieceofcake2/debug_kit</code>', $this->Html->link('pieceofcake2/debug_kit', 'https://github.com/pieceofcake2/debug_kit'));
        echo '</span>';
    endif;
    ?>
</p>

<h3><?php echo __d('cake_dev', 'Editing this Page'); ?></h3>
<p>
<?php
echo __d(
    'cake_dev',
    'To change the content of this page, edit: %s.<br />
To change its layout, edit: %s.<br />
You can also add some CSS styles for your pages at: %s.',
    'templates/Pages/home.ctp',
    'templates/Layouts/default.ctp',
    'webroot/css',
);
?>
</p>

<h3><?php echo __d('cake_dev', 'Getting Started'); ?></h3>
<ul>
    <li><?php echo $this->Html->link(__d('cake_dev', 'CakePHP 2 Documentation'), $docsUrl, ['target' => '_blank']); ?></li>
    <li><?php echo $this->Html->link(__d('cake_dev', 'Installation'), $docsUrl . 'installation.html', ['target' => '_blank']); ?></li>
    <li><?php echo $this->Html->link(__d('cake_dev', 'Configuration'), $docsUrl . 'development/configuration.html', ['target' => '_blank']); ?></li>
    <li><?php echo $this->Html->link(__d('cake_dev', 'Blog Tutorial'), $docsUrl . 'tutorials-and-examples/blog/blog.html', ['target' => '_blank']); ?></li>
</ul>

<h3><?php echo __d('cake_dev', 'Plugins'); ?></h3>
<p>
    <?php
    echo __d(
        'cake_dev',
        'See %s for the plugins updated to work with this version of CakePHP.',
        $this->Html->link(__d('cake_dev', 'Maintained Plugins'), $docsUrl . 'plugins/maintained-plugins.html', ['target' => '_blank']),
    );
    ?>
</p>

<h3><?php echo __d('cake_dev', 'More about Piece of Cake 2'); ?></h3>
<p>
<?php echo __d('cake_dev', 'Piece of Cake 2 is a community-maintained fork of CakePHP 2.x that runs on PHP 8. It is meant for maintaining existing CakePHP 2 applications; for new projects, use the latest CakePHP release.'); ?>
</p>

<ul>
    <li><a href="https://github.com/pieceofcake2"><?php echo __d('cake_dev', 'Piece of Cake 2 on GitHub'); ?></a>
    <ul><li><?php echo __d('cake_dev', 'The framework, the application skeleton and the maintained plugins'); ?></li></ul></li>
    <li><a href="https://github.com/pieceofcake2/cakephp/issues"><?php echo __d('cake_dev', 'CakePHP Issues'); ?></a>
    <ul><li><?php echo __d('cake_dev', 'Report bugs and request features'); ?></li></ul></li>
    <li><a href="<?php echo $docsUrl; ?>cakephp-overview/where-to-get-help.html"><?php echo __d('cake_dev', 'Where to Get Help'); ?></a>
    <ul><li><?php echo __d('cake_dev', 'Documentation, API and community resources'); ?></li></ul></li>
    <li><a href="<?php echo $docsUrl; ?>appendices/security-fixes.html"><?php echo __d('cake_dev', 'Security Fixes'); ?></a>
    <ul><li><?php echo __d('cake_dev', 'Vulnerabilities fixed in this fork'); ?></li></ul></li>
    <li><a href="https://cakephp.org"><?php echo __d('cake_dev', 'CakePHP'); ?></a>
    <ul><li><?php echo __d('cake_dev', 'The official CakePHP project, for new applications'); ?></li></ul></li>
</ul>
