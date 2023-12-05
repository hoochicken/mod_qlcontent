<?php
/**
 * @package        mod_qlcontent
 * @copyright      Copyright (C) 2023 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */

$strTitleTag = $params->get('cattitletag', 'h3');
?>
<?php if (isset($strField)) : ?>
    <<?php echo $strTitleTag; ?> class="<?php echo $strField; ?>">
    <?php echo $strLabel;
    if ((isset($arrItem->link_category) && 1 == $arrItem->link_category) || 'category' === $helper->type) {
        echo '<a href="' . $dataOfItems[$arrItem->id]->catlink . '">';
    }
    require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition');
    if (isset($arrItem->$strField)) {
        echo $arrItem->$strField;
    }
    if ((isset($arrItem->link_category) && 1 == $arrItem->link_category) || 'category' === $helper->type) {
        echo '</a>';
    } ?>
    </<?php echo $strTitleTag; ?>>
<?php endif; ?>

