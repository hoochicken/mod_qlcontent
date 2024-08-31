<?php
/**
 * @copyright      Copyright (C) 2024 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
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
$src = $arrItem->{$strField};
if ('' != $src) {
    if (isset($dataOfItems[$arrItem->id]->{$strField.'_alt'})) {
        $alt = $dataOfItems[$arrItem->id]->{$strField.'_alt'};
    } elseif (isset($dataOfItems[$arrItem->id]->title)) {
        $alt = $dataOfItems[$arrItem->id]->title;
    } else {
        $alt = $src;
    }
    $caption = '';
    if (isset($dataOfItems[$arrItem->id]->{$strField.'_caption'})) {
        $caption = $dataOfItems[$arrItem->id]->{$strField.'_caption'};
    }
    ?>
    <div class="<?php echo $strField; ?>">
        <?php require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition'); ?>
        <?php if ($params->get('link_titles')) {
            echo '<a href="'.$dataOfItems[$arrItem->id]->link.'">';
        } ?>
        <img
                src="<?php echo $src; ?>"
                alt="<?php echo $alt; ?>"
                title="<?php echo $caption; ?>"
        />
        <?php if ($params->get('link_titles')) {
            echo '</a>';
        } ?>
        <?php if ($params->get('showCaption') and isset($caption) and '' != $caption) { ?>
            <div class="caption"><?php echo $caption; ?></div>
        <?php } ?>
    </div>
    <?php
    unset($src, $alt, $caption);
}
