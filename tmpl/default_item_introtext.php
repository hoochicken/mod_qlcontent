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

if (!isset($strField)) return; ?>
<div class="<?php echo $strField; ?>">
    <?php
    echo $strLabel;
    require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition');
    if (in_array((int)$params->get('dots', 0), [1, 2,]) && !empty($dataOfItems[$arrItem->id]->fulltext)) {
        if ($params->get('striptags', false)) {
            $arrItem->$strField .= '...';
        } else {
            $posR = strripos($arrItem->$strField, '</p>');
            $arrItem->$strField = substr($arrItem->$strField, 0, $posR) . '...</p>';
            unset($posR);
        }
    }
    echo $arrItem->$strField;
    ?>
</div>
