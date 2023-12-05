<?php
/**
 * @package        mod_qlcontent
 * @copyright      Copyright (C) 2023 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

// no direct access
defined('_JEXEC') or die;

/** @var string $strTitleTag */
/** @var Joomla\Registry\Registry $params */
/** @var string $strLabel */
/** @var array $dataOfItems */
/** @var stdClass $arrItem */
/** @var modQlcontentHelper $helper */
?>
<?php if (isset($strField)) : ?>
    <div class="<?php echo $strField; ?>">
        <?php foreach ($arrItem->$strField as $numKey => $objValue) {
            echo FieldsHelper::render($objValue->context, 'field.render', ['field' => $objValue]);
        } ?>
    </div>
<?php endif; ?>

