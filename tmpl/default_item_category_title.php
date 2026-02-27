<?php
/**
 * @copyright    Copyright (C) 2025 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') || exit;

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
    <?php if ($arrItem->link_category) {
        echo sprintf('<a aria-label="%s" href="%s">', htmlspecialchars((string) $arrItem->{$strField}), $dataOfItems[$arrItem->id]->catlink);
    } ?>
    <?php require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition'); ?>
    <?php if (isset($arrItem->{$strField})) {
        echo $arrItem->{$strField};
    } ?>
    <?php if ($arrItem->link_category) {
        echo '</a>';
    } ?>
</div>
