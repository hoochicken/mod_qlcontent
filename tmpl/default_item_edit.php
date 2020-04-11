<?php
/**
 * @package		mod_qlcontent
 * @copyright	Copyright (C) 2018 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$lang = JFactory::getLanguage();
$langTag=substr($lang->get('tag'),0,2);
?>
<div class="icons">
    <div class="btn-group pull-right">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
        <ul class="dropdown-menu">
            <li class="edit-icon">
                <a href="?lang=<?php echo $langTag;?>&option=com_content&task=article.edit&a_id=<?php echo $arrItem->id;?>">
                    <?php echo JText::_('JACTION_EDIT');?><?php if(1!=$dataOfItems[$arrItem->id]->state) echo '<br />('.JText::_('JNOTPUBLISHEDYET').')';?></a>
            </li>
        </ul>
    </div>
</div>