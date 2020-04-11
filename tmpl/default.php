<?php
/**
 * @package        mod_qlcontent
 * @copyright    Copyright (C) 2018 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 || later; see LICENSE.txt
 */

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
// no direct access
defined('_JEXEC') || die; ?>
<div class="qlcontent <?php echo $moduleclass_sfx; ?>">
    <?php
    $class = $params->get('itemClass', 'col-xs-12 col-md-6');
    if (1 == $params->get('showBackButton') || 3 == $params->get('showBackButton')) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_back');
    }
    if (1 == $params->get('pagination') && isset($pagination)) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_pagination');
    }
    ?>
    <div class="items <?php echo $params->get('itemsClass', '') ?>">
        <?php
        if (is_array($arrItemsOrdered) || is_object($arrItemsOrdered)) {
            foreach ($arrItemsOrdered as $numKey => $arrItem):
                echo '<div class="item ' . $class . '">';
                require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item');
                echo '</div>';
            endforeach;
        } ?>
    </div>
    <?php
    if (1 == $params->get('pagination') && isset($pagination)) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_pagination');
    }
    if (2 == $params->get('showBackButton') || 3 == $params->get('showBackButton')) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_back');
    }
    ?>
</div>