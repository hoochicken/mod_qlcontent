<?php
/**
 * @copyright	Copyright (C) 2024 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
/** @var \Joomla\CMS\Pagination\Pagination $pagination */
?>
<div class="pagination paginationBox">
    <form action="#" method="post" name="adminForm">
        <?php echo $pagination->getLimitBox(); ?>
    </form>
</div>
