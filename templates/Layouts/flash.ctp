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
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var View $this
 * @var string $pageTitle
 * @var string $message
 * @var string $url
 * @var int $pause
 */

use Cake\Core\Configure;
use Cake\View\View;

?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?></title>
    <?php
    if (Configure::read('debug') == 0):
        echo sprintf('<meta http-equiv="Refresh" content="%s;url=%s" />', $pause, $url);
    endif;

    echo $this->Html->meta('icon');

    echo $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']);
    ?>
</head>
<body>
    <div class="flash-container">
        <h3><?php echo $this->Html->link($message, $url); ?></h3>
    </div>
</body>
</html>
