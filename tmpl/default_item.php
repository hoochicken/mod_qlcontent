<?php
/**
 * @package        mod_qlcontent
 * @copyright    Copyright (C) 2018 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$arr_qldate = array('created', 'modified', 'publish_up', 'created_time', 'modified_time');
$arr_qlimage = array('image_fulltext', 'image_intro', 'image');
$arr_qlurl = array('urla', 'urlb', 'urlc');
$arr_qlicon = array('show_print_icon', 'show_email_icon');

//iterate through data of single item
foreach ($arrItem as $strField => $v2) {
    if ('id' === $strField) {
        continue;
    }
    $strLabel = '';
    if (1 == $params->get('showlabels')) {
        $strLabel = preg_replace('/_/', ' ', ucwords($strField)) . ': ';
    }
	if (in_array($strField, $arr_qldate)) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_date');
    } elseif (in_array($strField, $arr_qlimage)) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_image');
    } elseif (in_array($strField, $arr_qlurl)) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_url');
    } elseif (isset($arr_qlicon) AND in_array($strField, $arr_qlicon)) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_icon');
    } elseif ('edit' == $strField AND true == $obj_helper->checkIfUserAuthorizedEdit()) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_edit');
    } elseif ('edit' == $strField) {

    } elseif ('introtext' == $strField) {
        require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_introtext');
    } else {
        $defaultFile = JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_' . $strField);
        if ('default.php' == substr($defaultFile, -11)) {
            require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_default');
        } else {
            require JModuleHelper::getLayoutPath('mod_qlcontent', 'default_item_' . $strField);
        }
    }
}