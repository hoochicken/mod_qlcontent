<?php

use Joomla\Registry\Registry;

/**
 * @copyright      Copyright (C) 2024 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var string $strTitleTag */
/** @var Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */
/** @var bool $above */
$arr_pagination = ['box', 'result', 'pages'];
foreach ($arr_pagination as $numKey => $strValue) {
    if ($above && in_array((int) $params->get('pagination_'.$strValue.'position'), [1, 3])) {
        include dirname(__FILE__).'/default_pagination'.ucwords($strValue).'.php';
    }
    if ($above && in_array((int) $params->get('pagination_'.$strValue.'position', 0), [2, 3])) {
        include dirname(__FILE__).'/default_pagination'.ucwords($strValue).'.php';
    }
}
$above = true;
