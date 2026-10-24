<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var View $this
 * @var string $name The escaped error message
 * @var string $message The escaped error message
 * @var string $url The escaped request URL
 * @var Exception $error The exception
 */

use Cake\Core\Configure;
use Cake\Utility\Debugger;
use Cake\View\View;

// Render the error in the minimal error layout, or in debug mode in the development error page
// of the core (dev_error layout), like CakePHP 5 does.
$this->layout = 'error';
// The page title would otherwise be the humanized view path ("Errors")
$this->assign('title', __d('cake', 'Error'));

if (Configure::read('debug')):
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');

    $this->start('file');
    if (isset($error) && $error instanceof Error):
?>
    <p>
        <strong><?php echo __d('cake_dev', 'Error in'); ?>:</strong>
        <?php echo h(Debugger::trimPath($error->getFile())); ?>, <?php echo __d('cake_dev', 'line %s', h($error->getLine())); ?>
    </p>
<?php
    endif;
    $this->end();

    return;
endif;
?>
<h2><?php echo $message; ?></h2>
<p class="error">
    <strong><?php echo __d('cake', 'Error'); ?>: </strong>
    <?php echo __d('cake', 'An Internal Error Has Occurred.'); ?>
</p>
