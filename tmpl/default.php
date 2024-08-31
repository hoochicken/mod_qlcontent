<?php
/**
 * @copyright      Copyright (C) 2024 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 || later; see LICENSE.txt
 */

use Joomla\CMS\Helper\ModuleHelper;

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR.'/components/com_fields/helpers/fields.php');

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var array $arrItemsOrdered */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */
/** @var string $moduleclass_sfx */

/** @var Joomla\Registry\Registry $params */

// no direct access
defined('_JEXEC') || exit; ?>
<div class="qlcontent <?php echo $moduleclass_sfx; ?>">
    <?php
    $class = $params->get('itemClass', 'col-xs-12 col-md-6');
if (in_array($params->get('showBackButton'), [1, 3])) {
    require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_back');
}
if ($params->get('pagination') && isset($pagination)) {
    require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_pagination');
}
?>
    <div class="items <?php echo $params->get('itemsClass', ''); ?>">
        <?php
    if (is_array($arrItemsOrdered) || is_object($arrItemsOrdered)) {
        foreach ($arrItemsOrdered as $numKey => $arrItem) {
            echo sprintf('<div class="item %s">', $class);

            require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item');
            echo '</div>';
        }
    } ?>
    </div>
    <?php
    if ($params->get('pagination') && isset($pagination)) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_pagination');
    }
if (2 === (int) $params->get('showBackButton', 0) || 3 === (int) $params->get('showBackButton', 0)) {
    require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_back');
}
?>
</div>
