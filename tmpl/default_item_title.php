<?php
/**
 * @copyright    Copyright (C) 2024 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
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

$strTitleTag = $params->get('titletag', 'h3');

if ($params->get('link_titles')) {
    echo '<a class="title" href="'.$dataOfItems[$arrItem->id]->link.'">';
} ?>
<<?php echo $strTitleTag; ?> class="<?php echo $strField; ?>">
<?php require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition'); ?>
<?php
if (isset($arrItem->{$strField})) {
    echo $arrItem->{$strField};
}
?>
</<?php echo $strTitleTag; ?>>
<?php if ($params->get('link_titles')) {
    echo '</a>';
} ?>
