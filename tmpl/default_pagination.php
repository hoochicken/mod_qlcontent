<?php
/**
 * @package        mod_qlcontent
 * @copyright      Copyright (C) 2022 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

$arr_pagination = array('box', 'result', 'pages');
foreach ($arr_pagination as $numKey => $strValue) {
    if (!isset($above) AND (1 == $params->get('pagination_' . $strValue . 'position') || 3 == $params->get('pagination_' . $strValue . 'position'))) {
        include dirname(__FILE__) . '/default_pagination' . ucwords($strValue) . '.php';
    }
    if (isset($above) AND (2 == $params->get('pagination_' . $strValue . 'position') || 3 == $params->get('pagination_' . $strValue . 'position'))) {
        include dirname(__FILE__) . '/default_pagination' . ucwords($strValue) . '.php';
    }
}
$above = 1;
