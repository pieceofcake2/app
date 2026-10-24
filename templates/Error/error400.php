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
use Cake\View\View;

// Render the error in the minimal error layout, or in debug mode in the development error page
// of the core (dev_error layout), like CakePHP 5 does.
$this->layout = 'error';
// The page title would otherwise be the humanized view path ("Errors")
$this->assign('title', __d('cake', 'Error'));

if (Configure::read('debug')):
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.php');

    $this->start('subheading');
?>
    <strong><?php echo __d('cake', 'Error'); ?>:</strong>
    <?php printf(
        __d('cake', 'The requested address %s was not found on this server.'),
        "<strong>'{$url}'</strong>"
    ); ?>
<?php
    $this->end();

    return;
endif;
?>
<h2><?php echo $message; ?></h2>
<p class="error">
    <strong><?php echo __d('cake', 'Error'); ?>: </strong>
    <?php printf(
        __d('cake', 'The requested address %s was not found on this server.'),
        "<strong>'{$url}'</strong>"
    ); ?>
</p>
