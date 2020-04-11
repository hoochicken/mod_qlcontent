<?php
/**
 * @package		mod_qlcontent
 * @copyright	Copyright (C) 2018 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.html.html');
require_once(JPATH_BASE.'/components/com_content/helpers/icon.php');
if (!isset($strField))return;
?>
<div class="<?php echo $strField; ?>">
    <?php require JModuleHelper::getLayoutPath('mod_qlcontent','default_item_showposition'); ?>
    <?php
    if (preg_match("/print/",$strField)) $jhtml='icon.print_popup';
    elseif (preg_match("/email/",$strField)) $jhtml='icon.email';
    if (''!=$jhtml AND isset($dataOfItems[$arrItem->id]->slug) AND isset($dataOfItems[$arrItem->id]->catid)) echo JHtml::_($jhtml,  $dataOfItems[$arrItem->id], $params); ?>
</div>