<?php
/**
 * @package		mod_qlcontent
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
if (!isset($strField))return;
?>
	<div class="<?php echo $strField; ?>">
        <?php require JModuleHelper::getLayoutPath('mod_qlcontent','default_item_showposition'); ?>
		<a target="<?php echo $dataOfItems[$arrItem->id]->{$strField.'target'};?>" class="<?php echo $dataOfItems[$arrItem->id]->{$strField.'class'};?>" rel="<?php echo $dataOfItems[$arrItem->id]->{$strField.'rel'};?>" href="<?php echo $arrItem->$strField;?>"><?php echo $dataOfItems[$arrItem->id]->{$strField.'text'};?></a>
	</div>
<?php unset($target);?>

