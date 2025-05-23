<?php
/**
 * @copyright      Copyright (C) 2025 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var Joomla\Registry\Registry $params */
/** @var QlContentErrors $errors */
?>
<div class="alert alert-danger <?php echo $params->get('strErrorClass', ''); ?>">
    <?php echo $errors->getErrorsAsString(); ?>
</div>
<?php
