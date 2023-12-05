<?php
/**
 * @package        mod_qlform
 * @copyright      Copyright (C) 2015 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
jimport('joomla.html.html');
//import the necessary class definition for formfield
jimport('joomla.form.formfield');

class FormFieldQLcategory extends FormField
{
    /**
     * The form field type.
     *
     * @var  string
     * @since 1.6
     */
    protected $type = 'qlcategory'; //the form field type see the name is the same

    /**
     * Method to retrieve the lists that resides in your application using the API.
     *
     * @return array The field option objects.
     * @since 1.6
     */
    protected function getInput()
    {
        $selected = $this->value;
        if ('' == $selected) $selected = $this->default;
        $options = $this->getOptions($selected);
        //echo '<pre>';print_r($options);echo '</pre>';
        $html = '';
        $html .= '<select name="' . $this->name . '" id="' . $this->id . '">';
        //while(list($k,$v)=each($options))
        foreach ($options as $k => $v) {
            $html .= '<option value="' . $v['value'] . '"';
            if (true == $v['selected']) $html .= ' selected="selected"';
            $html .= '>' . Text::_($v['label']) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    private function getOptions($selected)
    {
        $array = array
        (
            'category',
            'description',
            'note',
            'created_time',
            'modified_time',
            'user_name',
            'image',
        );
        $options = array();
        $options[0] = array(
            'label' => 'JNONE',
            'value' => 0,
            'selected' => false,
        );
        if ($selected === '0') $options[0]['selected'] = true;

        //while (list($k, $v) = each($array)) {
        foreach ($array as $k => $v) {
            $options[$v] = array
            (
                'label' => 'MOD_QLCONTENT_' . strtoupper($v),
                'value' => $v,
                'selected' => false,
            );
            if ($selected == $v) $options[$v]['selected'] = true;
        }
        return $options;
    }

}
