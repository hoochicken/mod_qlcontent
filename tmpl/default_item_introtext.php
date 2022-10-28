<?php
/**
 * @package        mod_qlcontent
 * @copyright      Copyright (C) 2022 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
if (!isset($strField)) return; ?>
<div class="<?php echo $strField; ?>">
    <?php
    echo $strLabel;
    require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_showposition');
    if (2 == $params->get('dots', 0) || (1 == $params->get('dots', 0) && !empty($dataOfItems[$arrItem->id]->fulltext))) {
        if (1 == $params->get('striptags', 0)) {
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
