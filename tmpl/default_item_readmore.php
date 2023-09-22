<?php
/**
 * @package		mod_qlcontent
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
/** @var Joomla\Registry\Registry $params */
// no direct access
defined('_JEXEC') or die;
if (!isset($strField))return;
if (''!=$dataOfItems[$arrItem->id]->fulltext OR 1==$params->get('readmoredisplay')) :
?>
	<div class="<?php echo $strField; ?>">
        <?php require JModuleHelper::getLayoutPath('mod_qlcontent','default_item_showposition'); ?>
		<a class="<?php echo $params->get('readmoreClass', 'btn btn-secondary'); ?>" href="<?php echo $dataOfItems[$arrItem->id]->link; ?>">
			<?php echo JText::_($arrItem->readmore);?>
		</a>
	</div>
<?php endif; ?>

