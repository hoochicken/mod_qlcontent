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

class JFormFieldArticle extends FormField
{
    protected $type = 'article';

    protected function getInput()
    {
        $selected = empty($this->value) ? $this->default ?? false : ($this->value);
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
            'title',
            'category',
            'introtext',
            'fulltext',
            'readmore',
            'created',
            'publish_up',
            'modified',
            'user_name',
            'image_intro',
            'image_fulltext',
            'jcfields',
            'urla',
            'urlb',
            'urlc',
            'edit',
            'show_print_icon',
            'show_email_icon',
            'hits',
            'tags',
        ];
        $options = [];
        $options[0] = [
            'label' => 'JNONE',
            'value' => 0,
            'selected' => '0' === $selected,
        ];

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
