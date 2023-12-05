<?php
/**
 * @package        mod_qlcontent
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class modQlcontentPagination extends JModelLegacy
{

    public $state = null;

    /**
     * method to construct state
     */
    public function __construct()
    {
        if (!isset($this->state)) $this->state = new Joomla\Registry\Registry();
    }

    /**
     * method to add pagination
     * @param array $arrItems
     * @return array $arrItems section of array above according to limits
     */
    public function addPagination($arrItems, $default_limit)
    {
        $this->setStates(count($arrItems), $default_limit);
        $arrItems = $this->limitItems($arrItems);
        return $arrItems;
    }

    /**
     * method to set states
     * @param integer $total number of all items
     * @param integer $default_limit number of limit
     */
    public function setStates($total, $default_limit)
    {
        $this->setState('total', $total);
        $app = Factory::getApplication();
        $input = $app->input;
        $limitstart = $input->get('limitstart', 0);
        $this->setState('limitstart', $limitstart);
        $app->getUserStateFromRequest('global.limitstart', 'limitstart', $limitstart);
        if ('' == $input->get('limit') and '' == $app->getUserStateFromRequest('global.limit', 'limit')) {
            $app->getUserStateFromRequest('global.limit', 'limit', $default_limit);
            $this->setState('limit', $default_limit);
        } else {
            $limit = $app->getUserStateFromRequest('global.limit', 'limit');
            $this->setState('limit', $limit);
        }
    }

    /**
     * method to add pagination
     * @param array $arrItems
     * @return array $arrItems section of array above according to limits
     */
    public function limitItems($arrItems)
    {
        if ($this->getState('limit') > 0) $arrItems = array_splice($arrItems, $this->getState('limitstart'), $this->getState('limit'));
        return $arrItems;
    }

    /**
     * method to get pagination html(??)
     * @return str pagination
     */
    public function getPagination()
    {
        jimport('joomla.html.pagination');
        $this->_pagination = new Pagination($this->getState('total'), $this->getState('limitstart'), $this->getState('limit'));
        return $this->_pagination;
    }
}
