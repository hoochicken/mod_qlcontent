<?php
/**
 * @copyright      Copyright (C) 2024 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

defined('_JEXEC') or exit;

jimport('joomla.application.component.model');
if (!class_exists('ContentHelperRoute')) {
    require_once JPATH_SITE.'/components/com_content/helpers/route.php';
}

PluginHelper::importPlugin('content');

class modQlcontentHelper
{
    public const FEATURED_ONLY = 1;
    public const FEATURED_BOTH = 2;
    public const FEATURED_NONE = 0;
    public const FEATURED_PREFERRED = 3;
    public string $type;
    public Registry $params;
    public $pagination;
    protected string $featured;
    protected int $limit;
    protected string $state;
    protected string $language;
    protected ?DatabaseDriver $bd;
    private stdClass $module;
    private string $order;

    public function __construct($module)
    {
        $this->db = Factory::getContainer()->get('DatabaseDriver');
        $this->query = $this->db->getQuery(true);
        $this->module = $module;
        $this->autoload();
        $registry = new JRegistry();
        $registry->loadString($module->params);
        $this->params = $registry;
    }

    public function autoload()
    {
        spl_autoload_register(function ($class) {
            if (file_exists(__DIR__.'/php/classes/'.$class.'.php')) {
                include __DIR__.'/php/classes/'.$class.'.php';
            }
        });
    }

    public function resetQuery()
    {
        $this->query = $this->db->getQuery(true);
    }

    public function setFeatured(string $col, int $featured)
    {
        $this->featured = (in_array($featured, [static::FEATURED_NONE, static::FEATURED_ONLY]))
            ? $col.'=\''.$featured.'\''
            : '';
    }

    public function setLimit($numLimit)
    {
        $this->limit = ($numLimit >= 0)
            ? $numLimit
            : 0;
    }

    public function setSelectState(string $col, int $state)
    {
        $query = '';
        // check rights (registered etc.)
        $query .= ($this->checkIfUserAuthorizedEdit())
            ? sprintf('((%s=\'0\') OR (%s=\'1\') OR (%s=\'2\'))', $col, $col, $col)
            : $col.'=\''.$state.'\'';

        // check state
        if ($state && 'con.state' === $col) {
            $date = '\''.date('Y-m-d H:i:s').'\'';
            $query .= 'AND (';
            $query .= '(con.publish_up <'.$date.' AND con.publish_down =\'0000-00-00 00:00:00\')';
            $query .= ' OR ';
            $query .= '(con.publish_up <'.$date.' AND con.publish_down IS NULL)';
            $query .= ' OR ';
            $query .= '(con.publish_up <'.$date.' AND con.publish_down >'.$date.')';
            $query .= ' OR ';
            $query .= '(con.publish_up IS NULL AND con.publish_down >'.$date.')';
            $query .= ')';
        }
        $this->state = $query;
    }

    public function checkIfUserAuthorizedEdit()
    {
        $user = Factory::getApplication()->getIdentity();

        return $user->authorise('core.edit.state', 'com_content') || $user->authorise('core.edit', 'com_content');
    }

    public function filterByTags(array $tags)
    {
        if (empty($tags)) {
            return;
        }
        $this->query->where('id IN(SELECT content_item_id FROM #__contentitem_tag_map WHERE tag_id IN('.implode(',', $tags).'))');
    }

    /**
     * set access filter.
     */
    public function setAccessFilter()
    {
        // Access filter
        $access = !ComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = Access::getAuthorisedViewLevels(Factory::getApplication()->getIdentity()->get('id'));
        if (0 >= count($authorised)) {
            return;
        }
        $where = '';
        $whereAccess = [];
        foreach ($authorised as $k => $v) {
            $whereAuthorised[$k] = '`access`=\''.$v.'\'';
        }
        $this->query->where('('.implode(' OR ', $whereAuthorised).')');
    }

    public function setLanguage(string $col, ?string $language = null)
    {
        $this->language = (!empty($language))
            ? $col.'=\''.$language.'\''
            : '';
    }

    public function setOrderBy(string $orderBy, string $orderDirection = 'ASC')
    {
        $this->order = !empty($orderBy)
            ? $orderBy.' '.strtoupper($orderDirection)
            : '';
    }

    public function getArticle(int $id): array
    {
        $items = $this->getDataArticle($id);

        foreach ($items as $k => $item) {
            $item = $this->addSlugCategory($item);
            $item = $this->addSlugItem($item, 'article');
            $item = $this->getItemAuthorization($item);
            $item = $this->addJson($item, 'images');
            $item = $this->addJson($item, 'urls');
            $item = $this->addJson($item, 'attribs');
            $items[$k] = $item;
        }

        return $items;
    }

    public function getCurrentArticle(string $field)
    {
        $input = Factory::getApplication()->input;
        $option = $input->get('option');
        $view = $input->get('view');
        if ('com_content' !== $option || 'article' !== $view) {
            return [];
        }
        $id = (string) $input->get('id', 0);
        $ids = explode(':', $id);
        $articleId = $ids[0];
        $articleTable = Table::getInstance('content');

        // $mvcFactory = Factory::getApplication()->bootComponent('com_content')->getMVCFactory();
        // $articleTable = $mvcFactory->createModel('Article', 'Administrator', ['ignore_request' => true]);

        // $objArticle = Factory::getApplication()->bootComponent('com_content')->getMVCFactory()->createTable($name, $prefix, $config)('content');
        $articleTable->load($articleId);

        return $articleTable->get($field);
    }

    public function getCurrentCategory($field)
    {
        $input = Factory::getApplication()->input;
        $option = $input->get('option');
        $view = $input->get('view');
        if (('com_content' == $option || 'com_contact' == $option) && ('category' == $view || 'categories' == $view)) {
            $ids = explode(':', (string) $input->get('id'));

            return $ids[0];
        }

        return [];
    }

    /**
     * method to get item.
     *
     * @return array object with all item data
     */
    public function getCategory($ids)
    {
        $arrItems = $this->getDataCategory($ids);
        $arrItems2 = [];
        foreach ($arrItems as $k => $v) {
            $v = $this->addSlugItem($v, 'category');
            $v = $this->addJson($v, 'params');
            $arrItems2[] = $v;
        }
        if (isset($arrItems2) and 0 < count($arrItems2)) {
            return $arrItems2;
        }
    }

    /**
     * method to get data fields from current article.
     *
     * @return mixed result object with table data on success or false on failure
     */
    public function getDataCategory($ids)
    {
        $where = $this->getWhereQueryOr($ids, 'cat.id');
        // $this->query=$this->db->getQuery(true);
        $this->resetQuery(); // query=$this->db->getQuery(true);
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
        if ('' != $this->language) {
            $this->query->where($this->language);
        }
        if ('' != $this->order) {
            $this->query->order($this->order);
        }
        if ('' != $this->limit) {
            $this->query->setLimit($this->limit);
        }
        if (is_array($arrItems = $this->askDb())) {
            return $arrItems;
        }

        return false;
    }

    /**
     * method to get data fields from current article.
     *
     * @param mixed $catids
     *
     * @return mixed result object with table data on success or false on failure
     */
    public function getArticles($catids)
    {
        if (!in_array('all', $catids)) {
            $where = $this->getWhereQueryOr($catids, 'con.catid');
            $this->query->where($where);
        }
        $this->query->select('con.id');
        $this->query->from('#__content AS con');
        $this->setAddendum();
        if ('' != $this->limit) {
            $this->query->setLimit($this->limit);
        }
        $arrItems = $this->askDb();
        if (is_array($arrItems)) {
            foreach ($arrItems as $k => $v) {
                $item = $this->getArticle($v->id);
                if (isset($item[0])) {
                    $arrItems[$k] = $item[0];
                }
            }
        }

        // echo '<pre>';print_r($arrItems);die;
        return $arrItems;
    }

    public function getCategoryChildren($ids): array
    {
        $where = $this->getWhereQueryOr($ids, 'cat.parent_id');
        $this->query = $this->db->getQuery(true);
        $this->query->select('cat.id');
        $this->query->from('`#__categories` AS cat');
        $this->query->where('cat.extension=\'com_content\'');
        $this->query->where($where);
        $this->setAddendum(0);

        return $this->askDb();
    }

    public function urlBehavior(stdClass $item): stdClass
    {
        foreach (range('a', 'c') as $k => $v) {
            if (isset($item->{'url'.$v}) and isset($item->{'target'.$v})) {
                switch ($item->{'target'.$v}) {
                    case 1:
                    case 2:
                        $target = '_blank';
                        $class = '';
                        $rel = '';

                        break;

                    case 3:
                        $target = '';
                        $class = 'modal';
                        $rel = '{size: {x: 1050, y: 500}, handler:\'iframe\'}';
                        if (!isset($modal)) {
                            HTMLHelper::_('behavior.modal');
                        }
                        $modal = 1;

                        break;

                    case 0:
                    default:
                        $target = '';
                        $class = '';
                        $rel = '';

                        break;
                }
                $item->{'url'.$v.'target'} = $target;
                $item->{'url'.$v.'class'} = $class;
                $item->{'url'.$v.'rel'} = $rel;
            }
            if (!isset($item->{'url'.$v.'text'}) || empty($item->{'url'.$v.'text'})) {
                $item->{'url'.$v.'text'} = $item->{'url'.$v} ?? '';
            }
        }

        return $item;
    }

    public function imageBehavior(stdClass $item, string $field): stdClass
    {
        if (empty($item->{$field})) {
            return $item;
        }

        if (isset($item->{$field.'_alt'})) {
            $alt = $item->{$field.'_alt'};
        } elseif (isset($item->title)) {
            $alt = $item->title;
        } else {
            $alt = $item->title;
        }
        if (isset($item->{$field.'_caption'})) {
            $caption = $item->{$field.'_caption'};
        }

        return $item;
    }

    public function readmoreBehavior(stdClass $item, Registry $params): string
    {
        if (isset($item->alternative_readmore) && !empty($item->alternative_readmore)) {
            $readmore = $item->alternative_readmore;
        } elseif (!empty($params->get('readmoretext', ''))) {
            $readmore = $params->get('readmoretext');
        } elseif ('COM_CONTENT_READ_MORE_TITLE' !== Text::sprintf('COM_CONTENT_READ_MORE_TITLE')) {
            $readmore = Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
        } else {
            $readmore = 'Read more';
        }

        return $readmore;
    }

    public function addPagination($arrItems, $params)
    {
        // require_once __DIR__.'/php/classes/modQlcontentPagination.php';
        $obj_pagination = new modQlcontentPagination();
        $arrItems = $obj_pagination->addPagination($arrItems, $params->get('pagination_limit'));
        $this->pagination = $obj_pagination->getPagination();

        return $arrItems;
    }

    public function prepareContent(stdClass $item): stdClass
    {
        PluginHelper::importPlugin('content');
        $item->text = $item->introtext ?? ($item->description ?? '');
        if (isset($item->fulltext)) {
            $item->text .= $item->fulltext;
        }
        $params = new Registry($item);
        $arrParamsDispatcher = ['com_content.article', &$item, &$params, 0];

        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new ContentPrepareEvent('onContentPrepare', $arrParamsDispatcher);
        $res = $dispatcher->dispatch('onCheckAnswer', $event);

        $item->introtext = $item->text;
        if (isset($item->description)) {
            $item->description = $item->text;
            unset($item->introtext);
        }
        if (isset($item->text)) {
            unset($item->text);
        }

        return $item;
    }

    public function cutString(string $string, int $count, string $unit = 'chars'): string
    {
        if ('chars' === $unit) {
            return substr(strip_tags($string), 0, $count);
        }

        $arrString = preg_split('/ /', $string);
        $arrString = array_slice($arrString, 0, $count);

        return implode(' ', $arrString);
    }

    public function get($var, $default = null)
    {
        return $this->{$var} ?? $default;
    }

    public function getRowCount(int $columnCount): int
    {
        return ($columnCount <= 0)
            ? 1
            : 12 / $columnCount;
    }

    protected function setAddendum(int $featured = self::FEATURED_ONLY)
    {
        $this->setAccessFilter();
        if (static::FEATURED_ONLY === $featured && !empty($this->featured)) {
            $this->query->where($this->featured);
        }
        if (!empty($this->language)) {
            $this->query->where($this->language);
        }
        if (!empty($this->state)) {
            $this->query->where($this->state);
        }
        if (!empty($this->order)) {
            $this->query->order($this->order);
        }
        if (!empty($this->limit)) {
            $this->query->setLimit($this->limit);
        }
    }

    protected function askDb(): array
    {
        if (is_numeric($this->limit) && 0 < $this->limit) {
            $this->db->setQuery($this->query, 0, $this->limit);
        } else {
            $this->db->setQuery($this->query);
        }

        return array_values($this->db->loadObjectList());
    }

    protected function getDataArticle(int $id): array
    {
        $this->query = $this->db->getQuery(true);
        $this->query->select('con.*, cat.id AS category_id, cat.alias AS category_alias, cat.title AS category, us.name AS user_name, us.username AS user_username');
        $this->query->from('`#__content` AS con, `#__categories` AS cat, `#__users` AS us');
        $this->query->where(sprintf('con.id=\'%s\'', $id));
        $this->query->where('con.catid=cat.id');
        $this->query->group('id');

        return $this->askDb();
    }

    /**
     * method to add slugs to item.
     *
     * @param object $item
     * @param mixed  $router
     *
     * @return result $item with new links routed via JRouter
     */
    protected function addSlugItem($item, $router)
    {
        $item->slug = $item->id.':'.$item->alias;
        if ('article' == $router) {
            $item->link = Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
        } elseif ('category' == $router) {
            $item->link = $item->catlink = Route::_(ContentHelperRoute::getCategoryRoute($item->slug));
        }

        return $item;
    }

    /**
     * method to add slugs of Category to item.
     *
     * @param object $item
     * @param bool true if category, false if item's article
     *
     * @return result $item with new links routed via JRouter
     */
    protected function addSlugCategory($item)
    {
        $item->catslug = $item->catid.':'.$item->category_alias;
        $item->catlink = Route::_(ContentHelperRoute::getCategoryRoute($item->catslug));

        return $item;
    }

    protected function getItemAuthorization($item)
    {
        $access = !ComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = Access::getAuthorisedViewLevels(Factory::getApplication()->getIdentity()->getParam('id') ?? 0);
        if ($access || in_array($item->access, $authorised)) {
            $item->link = Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
        } else {
            $item->link = Route::_('index.php?option=com_users&view=login');
        }

        return $item;
    }

    protected function addJson($item, $strField)
    {
        $var = null;
        if (isset($item->{$strField})) {
            $var = json_decode($item->{$strField});
        }
        if (is_object($var)) {
            foreach ($var as $k => $v) {
                $item->{$k} = $v.'';
            }
        }

        return $item;
    }

    /**
     * method to get data fields from current article.
     *
     * @return mixed result object with table data on success or false on failure
     */
    protected function getWhereQueryOr($arrIds, $strColumnName): string
    {
        if (is_array($arrIds)) {
            $strQuery = $strColumnName.' IN ('.implode(',', $arrIds).')';
        } else {
            $strQuery = ''.$strColumnName.'=\''.$arrIds.'\'';
        }

        return $strQuery;
    }
}
