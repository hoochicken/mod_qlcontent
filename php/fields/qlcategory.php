<?php
/**
 * @copyright      Copyright (C) 2025 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') || exit;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldQlcategory extends FormField
{
    protected $type = 'qlcategory'; // the form field type see the name is the same

    protected function getInput()
    {
        $selected = $this->value ?? $this->default;

        $options = $this->getOptions($selected);
        $html = '';
        $html .= '<select name="'.$this->name.'" id="'.$this->id.'" class="form-select">';
        foreach ($options as $v) {
            $html .= '<option value="'.$v['value'].'"';
            if ($v['selected']) {
                $html .= ' selected="selected"';
            }
            $html .= '>'.Text::_($v['label']).'</option>';
        }

        return $html . '</select>';
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
            ],
        ];
        if ('0' === $selected) {
            $options[0]['selected'] = true;
        }

        foreach ($array as $v) {
            $options[$v] = [
                'label' => 'MOD_QLCONTENT_'.strtoupper($v),
                'value' => $v,
                'selected' => (string) $selected === (string) $v,
            ];
        }

        return $options;
    }
}
