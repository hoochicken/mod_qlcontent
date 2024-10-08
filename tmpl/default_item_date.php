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

if (!isset($strField)) {
    return;
}
$date = HTMLHelper::_('date', $arrItem->{$strField}, $params->get('dateformat')); ?>
    <div class="<?php echo $strField; ?>">
        <?php echo $strLabel; ?>
        <?php require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition'); ?>
        <?php echo $date; ?>
    </div>
<?php unset($date); ?>
