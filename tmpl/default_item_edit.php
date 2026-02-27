<?php
/**
 * @copyright      Copyright (C) 2025 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */

defined('_JEXEC') || exit;
$lang = Factory::getLanguage();
$langTag = substr((string) $lang->get('tag'), 0, 2);
?>
<div class="icons">
    <div class="btn-group pull-right">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
        <ul class="dropdown-menu">
            <li class="edit-icon">
                <a href="?lang=<?php echo $langTag; ?>&option=com_content&task=article.edit&a_id=<?php echo $arrItem->id; ?>">
                    <?php echo Text::_('JACTION_EDIT'); ?><?php if (1 !== (int) $dataOfItems[$arrItem->id]->state) {
                        echo '<br />('.JText::_('JNOTPUBLISHEDYET').')';
                    } ?></a>
            </li>
        </ul>
    </div>
</div>
