<?php

/**
 * @copyright    Copyright (C) 2024 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or exit;

/** @var Joomla\Registry\Registry $params */
/** @var int $i */
/** @var int $strField */

if ($params->get('showCaption', false)) {
    echo '('.$i.':'.$strField.')';
}
