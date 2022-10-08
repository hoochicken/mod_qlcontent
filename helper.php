<?php
/**
 * @package        mod_qlcontent
 * @copyright    Copyright (C) 2022 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
if (!class_exists('ContentHelperRoute')) require_once JPATH_SITE . '/components/com_content/helpers/route.php';


JPluginHelper::importPlugin('content');

class modQlcontentHelper
{
    protected $featured;
    protected $limit;
    protected $state;
    protected $language;
    protected $bd;
    public $type;
    public $params;
    private $module;
    private $order;
    public $pagination;

    /**
     * constructor
     * @param $module
     */
    public function __construct($module)
    {
        $this->db = JFactory::getDbo();
        $this->query = $this->db->getQuery(true);
        $this->module = $module;
        $this->autoload();
        $this->params = $module->params;
    }

    /**
     *
     */
    function autoload()
    {
        spl_autoload_register(function ($class) {
            if (file_exists(__DIR__ . '/php/classes/' . $class . '.php'))
                include __DIR__ . '/php/classes/' . $class . '.php';
        });
    }

    /**
     *
     */
    public function resetQuery()
    {
        $this->query = $this->db->getQuery(true);
    }

    /**
     * method to set if featured or not
     * @param integer $featured
     * @return string $featured part of query
     */
    public function setFeatured($col, $featured)
    {
        if (0 == $featured OR 1 == $featured) $this->featured = $col . '=\'' . $featured . '\'';
        else $this->featured = '';
    }

    /**
     * method to set limit
     * @param $numLimit
     * @return string $limit part of query
     */
    public function setLimit($numLimit)
    {
        if ($numLimit >= 0) $this->limit = $numLimit;
        else $this->limit = '';
    }

    /**
     * method to set published
     * @param $col
     * @param $state
     * @return string $limit part of query
     */
    public function setSelectState($col, $state)
    {
        //if ('con.state'!=$col) return;
        $query = '';
        /*check rights (registered etc.)*/
        if (true == $this->checkIfUserAuthorizedEdit()) $query .= '((' . $col . '=\'0\') OR (' . $col . '=\'1\') OR (' . $col . '=\'2\'))';
        else $query .= $col . '=\'' . $state . '\'';

        /*check state*/
        if (1 == $state AND 'con.state' == $col) {
            $date = '\'' . date('Y-m-d H:i:s') . '\'';
            $query .= 'AND (';
            $query .= '(con.publish_up <' . $date . ' AND con.publish_down =\'0000-00-00 00:00:00\')';
            $query .= ' OR ';
            $query .= '(con.publish_up <' . $date . ' AND con.publish_down >' . $date . ')';
            $query .= ')';
        }
        $this->state = $query;
    }

    /**
     * @return bool
     */
    public function checkIfUserAuthorizedEdit()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.edit.state', 'com_content') OR $user->authorise('core.edit', 'com_content')) return true;
        return false;
    }

    /**
     * set access filter
     */
    public function setAccessFilter()
    {
        // Access filter
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        if (0 >= count($authorised)) return;
        $where = '';
        $whereAccess = array();
        foreach ($authorised as $k => $v) $whereAuthorised[$k] = '`access`=\'' . $v . '\'';
        $this->query->where('(' . implode(' OR ', $whereAuthorised) . ')');
    }

    /**
     * method to set language
     * @param integer $language
     * @return string $language part of query
     */
    public function setLanguage($col, $language)
    {
        if ('' != $language) $this->language = $col . '=\'' . $language . '\'';
        else $this->language = '';
    }

    /**
     * method to set order by
     * @param $orderBy
     * @param $orderDirection
     * @return string $language part of query
     */
    public function setOrderBy($orderBy, $orderDirection)
    {
        if ('' != $orderBy) $this->order = $orderBy . ' ' . strtoupper($orderDirection);
        else $this->order = '';
    }

    /**
     * method to set some parts of query
     * @param int $featured
     * @return string part of query
     */
    protected function setAddendum($featured = 1)
    {
        $this->setAccessFilter();
        if (1 == $featured AND '' != $this->featured) $this->query->where($this->featured);
        if ('' != $this->language) $this->query->where($this->language);
        if ('' != $this->state) $this->query->where($this->state);
        if ('' != $this->order) $this->query->order($this->order);
        if ('' != $this->limit) $this->query->setLimit($this->limit);
    }

    /**
     * method to get data from db
     * @return result object with table data
     * @internal param string $query sql query
     */
    protected function askDb()
    {
        if (is_numeric($this->limit) AND 0 < $this->limit) $this->db->setQuery($this->query, 0, $this->limit);
        //if(97==$this->module->id)die($this->query);
        else $this->db->setQuery($this->query);
        return $this->db->loadObjectList();
    }

    /**
     * method to get item
     * @param string $numId
     * @return result $arrItems array  with all item data
     */
    public function getArticle($numId)
    {
        $arrItems = $this->getDataArticle($numId);
        //echo '<pre>';print_r($arrItems);die;
        //while (list($k, $item) = each($arrItems)) {
        foreach ($arrItems as $k => $item) {
            $item = $this->addSlugCategory($item);
            $item = $this->addSlugItem($item, 'article');
            $item = $this->getItemAuthorization($item);
            $item = $this->addJson($item, 'images');
            $item = $this->addJson($item, 'urls');
            $item = $this->addJson($item, 'attribs');
            $arrItems[$k] = $item;
        }
        return $arrItems;
    }

    /**
     * method to get data fields from current article
     * @param string $numId
     * @return result object with table data
     */
    protected function getDataArticle($numId)
    {
        $this->query = $this->db->getQuery(true);
        $this->query->select('con.*, cat.id AS category_id, cat.alias AS category_alias, cat.title AS category, us.name AS user_name, us.username AS user_username');
        $this->query->from('`#__content` AS con, `#__categories` AS cat, `#__users` AS us');
        //$this->query->where('con.id=\''.$numId.'\' AND con.catid=cat.id AND con.created_by=us.id');
        $this->query->where('con.id=\'' . $numId . '\'');
        $this->query->where('con.catid=cat.id');
        $this->query->group('id');
        //die($this->query);
        return $this->askDb();
    }

    /**
     * method to get fields from current article
     * @param string $strField
     * @return result field value
     * @throws Exception
     */
    public function getCurrentArticle($strField)
    {
        $objInput = JFactory::getApplication()->input;
        $strOption = $objInput->get('option');
        $strView = $objInput->get('view');
        if ('com_content' !== $strOption || 'article' !== $strView) {
            return false;
        }
        $strId = (string)$objInput->get('id');
        $arrIds = explode(':', $strId);
        $numArticleId = $arrIds[0];
        $objArticle = JTable::getInstance('content');
        $objArticle->load($numArticleId);
        $return = $objArticle->get($strField);
        return $return;

    }

    /**
     * method to get fields from current category
     * @param string $strField
     * @return result field value
     * @throws Exception
     */
    public function getCurrentCategory($strField)
    {
        $input = JFactory::getApplication()->input;
        $option = $input->get('option');
        $view = $input->get('view');
        if (('com_content' == $option OR 'com_contact' == $option) AND ('category' == $view OR 'categories' == $view)) {
            $numIds = explode(':', (string)$input->get('id'));
            return $numIds[0];
        } else return false;
    }

    /**
     * method to add slugs to item
     * @param object $item
     * @return result $item with new links routed via JRouter
     */
    protected function addSlugItem($item, $router)
    {
        $item->slug = $item->id . ':' . $item->alias;
        if ('article' == $router) $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
        elseif ('category' == $router) $item->link = $item->catlink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->slug));

        return $item;
    }

    /**
     * method to add slugs of Category to item
     * @param object $item
     * @param bool true if category, false if item's article
     * @return result $item with new links routed via JRouter
     */
    protected function addSlugCategory($item)
    {
        $item->catslug = $item->catid . ':' . $item->category_alias;
        $item->catlink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug));
        return $item;
    }

    /**
     * method to check authorization
     * @param object $item
     * @return result $item with new links if not authorized
     */
    protected function getItemAuthorization($item)
    {
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        if ($access || in_array($item->access, $authorised)) $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
        else $item->link = JRoute::_('index.php?option=com_users&view=login');
        return $item;
    }

    /**
     * method to add image information to item
     * @param object $item
     * @param $strField
     * @return result $item with images keys
     */
    protected function addJson($item, $strField)
    {
        $var = null;
        if (isset($item->$strField)) {
            $var = json_decode($item->$strField);
        }
        if (is_object($var)) {
            foreach ($var as $k => $v) {
                $item->$k = $v . '';
            }
        }
        return $item;
    }

    /**
     * method to get item
     * @param $numIds
     * @return array object with all item data
     */
    public function getCategory($numIds)
    {
        $arrItems = $this->getDataCategory($numIds);
        $arrItems2 = array();
        foreach($arrItems as $k => $v) {
            $v = $this->addSlugItem($v, 'category');
            $v = $this->addJson($v, 'params');
            $arrItems2[] = $v;
        }
        if (isset($arrItems2) AND 0 < count($arrItems2)) return $arrItems2;
    }

    /**
     * method to get data fields from current article
     * @param $numIds
     * @return mixed result object with table data on success or false on failure
     */
    public function getDataCategory($numIds)
    {
        $where = $this->getWhereQueryOr($numIds, 'cat.id');
        //$this->query=$this->db->getQuery(true);
        $this->resetQuery();//query=$this->db->getQuery(true);
        $this->query->select('cat.*,cat.title as category, us.name AS user_name, us.username AS user_username');
        $this->query->from('`#__categories` AS cat,`#__users` AS us');
        $this->query->where($where);
        $this->query->where('us.id=cat.created_user_id');
        $this->setAddendum(0);
        if (is_array($arrItems = $this->askDb()) && count($arrItems) > 0) {
            return $arrItems;
        }

        $this->query = $this->db->getQuery(true);
        $this->query->select('cat.*,cat.title as category, us.name AS user_name, us.username AS user_username');
        $this->query->from('`#__categories` AS cat,`#__users` AS us');
        $this->query->where($where);
        $this->query->where('us.id=cat.created_user_id');
        if ('' != $this->language) $this->query->where($this->language);
        if ('' != $this->order) $this->query->order($this->order);
        if ('' != $this->limit) $this->query->setLimit($this->limit);
        if (is_array($arrItems = $this->askDb())) return $arrItems;
        else return false;
    }

    /**
     * method to get data fields from current article
     * @param array $arrCatid
     * @return mixed result object with table data on success or false on failure
     */
    public function getArticles($arrCatid)
    {
        if (!in_array('all', $arrCatid)) {
            $where = $this->getWhereQueryOr($arrCatid, 'con.catid');
            $this->query->where($where);
        }
        $this->query->select('con.id');
        $this->query->from('#__content AS con');
        $this->setAddendum();
        if ('' != $this->limit) $this->query->setLimit($this->limit);
        $arrItems = $this->askDb();
        if (is_array($arrItems)) foreach ($arrItems as $k => $v) {
            $item = $this->getArticle($v->id);
            if (isset($item[0])) $arrItems[$k] = $item[0];
        }
        //echo '<pre>';print_r($arrItems);die;
        return $arrItems;
    }

    /**
     * method to get data fields from current article
     * @param $arrIds
     * @param $strColumnName
     * @return mixed result object with table data on success or false on failure
     */
    protected function getWhereQueryOr($arrIds, $strColumnName)
    {
        if (is_array($arrIds)) {
            $strQuery = $strColumnName . ' IN (' . implode(',', $arrIds) . ')';
        } else {
            $strQuery = '' . $strColumnName . '=\'' . $arrIds . '\'';
        }
        //die($strQuery);
        return $strQuery;
    }

    /**
     * method to get data fields from current article
     * @param string $numIds
     * @return mixed result object with table data on success or false on failure
     */
    public function getCategoryChildren($numIds)
    {
        $where = $this->getWhereQueryOr($numIds, 'cat.parent_id');
        $this->query = $this->db->getQuery(true);
        $this->query->select('cat.id');
        $this->query->from('`#__categories` AS cat');
        $this->query->where('cat.extension=\'com_content\'');
        $this->query->where($where);
        $this->setAddendum(0);
        //die($this->query);
        return $this->askDb();
    }

    /**
     * method to add targets to urls
     * @param object $item with data
     * @return object result object with additional url data
     */
    public function urlBehavior($item)
    {
        foreach (range('a', 'c') as $k => $v) {
            if (isset($item->{'url' . $v}) AND isset($item->{'target' . $v})) {
                switch ($item->{'target' . $v}) {
                    case 0:
                        $target = '';
                        $class = '';
                        $rel = '';
                        break;
                    case 1:
                        $target = '_blank';
                        $class = '';
                        $rel = '';
                        break;
                    case 2:
                        $target = '_blank';
                        $class = '';
                        $rel = '';
                        break;
                    case 3:
                        $target = '';
                        $class = 'modal';
                        $rel = '{size: {x: 1050, y: 500}, handler:\'iframe\'}';
                        if (!isset($modal)) JHTML::_('behavior.modal');
                        $modal = 1;
                        break;
                    default :
                        $target = '';
                        $class = '';
                        $rel = '';
                        break;
                }
                $item->{'url' . $v . 'target'} = $target;
                $item->{'url' . $v . 'class'} = $class;
                $item->{'url' . $v . 'rel'} = $rel;
            }
            if ((!isset($item->{'url' . $v . 'text'}) OR (isset($item->{'url' . $v . 'text'}) AND '' == $item->{'url' . $v . 'text'})) AND isset($item->{'url' . $v})) $item->{'url' . $v . 'text'} = $item->{'url' . $v};
        }
        return $item;
    }

    /**
     * method to add targets to urls
     * @param object $item with data
     * @return object result object with additional url data
     */
    public function imageBehavior($item, $strField)
    {
        if (isset($item->$strField) AND '' != $item->$strField) {
            if (isset($item->{$strField . '_alt'})) $alt = $item->{$strField . '_alt'};
            elseif (isset($item->title)) $alt = $item->title;
            else $alt = $item->title;
            if (isset($item->{$strField . '_caption'})) $caption = $item->{$strField . '_caption'};
        }
        return $item;
    }

    /**
     * method to add targets to urls
     * @param object $item with data
     * @return object result object with additional url data
     */
    public function readmoreBehavior($item, $params)
    {
        if (isset($item->alternative_readmore) AND '' != $item->alternative_readmore) $readmore = $item->alternative_readmore;
        elseif ('' != $params->get('readmoretext')) $readmore = $params->get('readmoretext');
        elseif ('COM_CONTENT_READ_MORE_TITLE' != JText::sprintf('COM_CONTENT_READ_MORE_TITLE')) $readmore = JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
        else $readmore = "Read more";
        return $readmore;
    }

    /**
     * method to add pagination
     * @param object items
     * @param object params containing limit
     * @return object result object with paginated
     */
    public function addPagination($arrItems, $params)
    {
        //require_once __DIR__.'/php/classes/modQlcontentPagination.php';
        $obj_pagination = new modQlcontentPagination;
        $arrItems = $obj_pagination->addPagination($arrItems, $params->get('pagination_limit'));
        $this->pagination = $obj_pagination->getPagination();
        //print_r($this->pagination);die;
        return $arrItems;
    }

    /**
     * method to prepare content
     * @param object item
     * @return object item
     */
    public function prepareContent($objItem)
    {
        JPluginHelper::importPlugin('content');
        $objItem->text = $objItem->introtext;
        if (isset($objItem->fulltext)) {
            $objItem->text .= $objItem->fulltext;
        }
        $dispatcher = JEventDispatcher::getInstance();
        $params = new JRegistry($objItem);
        $dispatcher->trigger('onContentPrepare', array('com_content.article', &$objItem, &$params, 0));
        $objItem->introtext = $objItem->text;
        unset($objItem->text);
        return $objItem;
    }

    /**
     * method to prepare content
     * @param string string to be cut
     * @param integer $count length of string set back
     * @param string $unit for counting
     * @return string
     */
    public function cutString($string, $count, $unit = 'chars')
    {
        if ('chars' == $unit) return $string = substr(strip_tags($string), 0, $count);
        else {
            $arrString = preg_split('/ /', $string);
            $arrString = array_slice($arrString, 0, $count);
            $string = implode(' ', $arrString);
            return $string;
        }
    }

    /** method to get class variables
     * @param string $var name of (private or protected or public) variable
     * @return mixed variable value
     */
    public function get($var)
    {
        if (isset($this->$var)) return $this->$var;
    }

    /**
     * @param $int
     * @return float|int
     */
    function getRowCount($int)
    {
        return 12 / $int;
        switch ($int) {
            case 12:
                $rowCount = 1;
                break;
            case 6:
                $rowCount = 2;
                break;
            case 4:
                $rowCount = 3;
                break;
            case 3:
                $rowCount = 4;
                break;
            case 1:
                $rowCount = 12;
                break;
        }
        return $rowCount;
    }

}