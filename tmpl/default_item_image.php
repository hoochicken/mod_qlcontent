<?php
/**
 * @package		mod_qlcontent
 * @copyright	Copyright (C) 2018 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
if (!isset($strField))return;
$src=$arrItem->$strField;
if (''!=$src) :
    if (isset($dataOfItems[$arrItem->id]->{$strField.'_alt'}))$alt=$dataOfItems[$arrItem->id]->{$strField.'_alt'};
    elseif (isset($dataOfItems[$arrItem->id]->title)) $alt=$dataOfItems[$arrItem->id]->title;
    else $alt=$src;
    $caption='';
    if (isset($dataOfItems[$arrItem->id]->{$strField.'_caption'}))$caption=$dataOfItems[$arrItem->id]->{$strField.'_caption'};
    ?>
    <div class="<?php echo $strField; ?>">
        <?php require JModuleHelper::getLayoutPath('mod_qlcontent','default_item_showposition'); ?>
        <?php if (1==$params->get('link_titles')) echo '<a href="'.$dataOfItems[$arrItem->id]->link.'">';?>
        <img
            src="<?php echo $src;?>"
            alt="<?php echo $alt;?>"
            title="<?php echo $caption;?>"
            />
        <?php if (1==$params->get('link_titles')) echo '</a>';?>
        <?php if (1==$params->get('showCaption') AND isset($caption) AND ''!=$caption):?><div class="caption"><?php echo $caption;?></div><?php endif;?>
    </div>
    <?php
    unset($src);unset($alt);unset($caption);
endif;