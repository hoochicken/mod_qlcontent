<?php
/**
 * @copyright	Copyright (C) 2025 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or exit;

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */

if (!isset($strField)) {
    return;
}
?>
	<div class="<?php echo $strField; ?>">
        <?php require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition'); ?>
		<a aria-label="<?= htmlspecialchars($dataOfItems[$arrItem->id]->title) ?>" target="<?php echo $dataOfItems[$arrItem->id]->{$strField.'target'}; ?>" class="<?php echo $dataOfItems[$arrItem->id]->{$strField.'class'}; ?>" rel="<?php echo $dataOfItems[$arrItem->id]->{$strField.'rel'}; ?>" href="<?php echo $arrItem->{$strField}; ?>"><?php echo $dataOfItems[$arrItem->id]->{$strField.'text'}; ?></a>
	</div>
<?php unset($target); ?>
