<?php
/**
 * @copyright      Copyright (C) 2024 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or exit;

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */
$arr_qldate = ['created', 'modified', 'publish_up', 'created_time', 'modified_time'];
$arr_qlimage = ['image_fulltext', 'image_intro', 'image'];
$arr_qlurl = ['urla', 'urlb', 'urlc'];
$arr_qlicon = ['show_print_icon', 'show_email_icon'];

// iterate through data of single item
foreach ($arrItem as $strField => $v2) {
    if ('id' === $strField) {
        continue;
    }
    $strLabel = '';
    if ($params->get('showlabels')) {
        $strLabel = preg_replace('/_/', ' ', ucwords($strField)).': ';
    }
    if (in_array($strField, $arr_qldate)) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_date');
    } elseif (in_array($strField, $arr_qlimage)) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_image');
    } elseif (in_array($strField, $arr_qlurl)) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_url');
    } elseif (isset($arr_qlicon) && in_array($strField, $arr_qlicon)) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_icon');
    } elseif ('edit' == $strField && $helper->checkIfUserAuthorizedEdit()) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_edit');
    } elseif ('edit' == $strField) {
    } elseif ('introtext' == $strField) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_introtext');
    } else {
        $defaultFile = ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_'.$strField);
        if ('default.php' == substr($defaultFile, -11)) {
            require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_default');
        } else {
            require ModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_'.$strField);
        }
    }
}
