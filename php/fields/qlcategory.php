<?php
/**
 * @package        mod_qlcontent
 * @copyright      Copyright (C) 2023 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldQlcategory extends FormField
{
    protected $type = 'qlcategory'; //the form field type see the name is the same

    protected function getInput()
    {
        $selected = $this->value ??  $this->default;

        $options = $this->getOptions($selected);
        $html = '';
        $html .= '<select name="' . $this->name . '" id="' . $this->id . '" class="form-select">';
        foreach ($options as $k => $v) {
            $html .= '<option value="' . $v['value'] . '"';
            if ($v['selected']) {
                $html .= ' selected="selected"';
            }
            $html .= '>' . Text::_($v['label']) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    private function getOptions($selected)
    {
        $array = [
            'category',
            'description',
            'note',
            'created_time',
            'modified_time',
            'user_name',
            'image',
        ];
        $options = [
            0 => [
                'label' => 'JNONE',
                'value' => 0,
                'selected' => false,
            ]
        ];
        if ($selected === '0') {
            $options[0]['selected'] = true;
        }

        foreach ($array as $k => $v) {
            $options[$v] = [
                'label' => 'MOD_QLCONTENT_' . strtoupper($v),
                'value' => $v,
                'selected' => (string)$selected === (string)$v,
            ];
        }
        return $options;
    }

}
