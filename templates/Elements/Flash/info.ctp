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
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var View $this
 * @var string $key
 * @var string $message
 * @var array $params
 */

use Cake\View\View;

if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div id="<?php echo h($key); ?>Message" class="message" onclick="this.classList.add('hidden');"><?php echo $message; ?></div>
