<?php
/**
 * @package        mod_qlcontent
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
/** @var Joomla\Registry\Registry $params */
// no direct access
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */

if (!isset($strField)) return;
if ('' != $dataOfItems[$arrItem->id]->fulltext or 1 == $params->get('readmoredisplay')) :
    ?>
    <div class="<?php echo $strField; ?>">
        <?php require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition'); ?>
        <a class="<?php echo $params->get('readmoreClass', 'btn btn-secondary'); ?>"
           href="<?php echo $dataOfItems[$arrItem->id]->link; ?>">
            <?php echo Text::_($arrItem->readmore); ?>
        </a>
    </div>
<?php endif; ?>

