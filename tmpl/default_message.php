<?php
/**
 * @package        mod_qlcontent
 * @copyright      Copyright (C) 2023 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */
/** @var \Joomla\CMS\Pagination\Pagination $pagination */
?>
<div class="<?php echo $params->get('strMessageClass', ''); ?>">
    <?php echo $params->get('strEmptyMessage', ''); ?>
</div>
<?php
