<?php
/**
 * @copyright      Copyright (C) 2024 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or exit;

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */

jimport('joomla.html.html');

require_once JPATH_BASE.'/components/com_content/helpers/icon.php';
if (!isset($strField)) {
    return;
}
?>
<div class="<?php echo $strField; ?>">
    <?php require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition'); ?>
    <?php
    if (preg_match('/print/', $strField)) {
        $jhtml = 'icon.print_popup';
    } elseif (preg_match('/email/', $strField)) {
        $jhtml = 'icon.email';
    }
if (!empty($jhtml) && isset($dataOfItems[$arrItem->id]->slug, $dataOfItems[$arrItem->id]->catid)) {
    echo HTMLHelper::_($jhtml, $dataOfItems[$arrItem->id], $params);
} ?>
</div>
