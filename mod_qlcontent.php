<?php
/**
 * @copyright    Copyright (C) 2025 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 || later; see LICENSE.txt
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;

defined('_JEXEC') || exit;

/** @var Registry $params */
/** @var stdClass $module */
require_once __DIR__ . '/php/classes/QlContentErrors.php';
$errors = new QlContentErrors();
// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';
$helper = new modQlcontentHelper($module, $errors);

try {
    $id = '';
    $todo = (int)$params->get('todo', 0);
    $isCategoryView = true;
    $featured = (int)$params->get('featured', modQlcontentHelper::FEATURED_BOTH);
    $tags = $params->get('tags', []);
    $limitForQuery = modQlcontentHelper::FEATURED_PREFERRED === $featured
        ? 0
        : (int)$params->get('count', 0);
    $limit = $params->get('count', 0);
    if (!is_array($tags)) {
        $tags = [];
    }

    $input = Factory::getApplication()->input;
    $arrItems = [];
    $above = false;

    $moduleIdCheck = 0;
    if ($moduleIdCheck === $module->id) {
        print_r($todo);

        exit;
    }

    if (0 != $params->get('checkToken', 0) && !Session::checkToken($params->get('checkToken', 'post'))) {
        return;
    }
    if (39 >= $todo) {
        $helper->type = 'article';
        $helper->setSelectState('con.state', $params->get('state'));
        $helper->setFeatured('con.featured', $featured);
        $helper->setLanguage('con.language', $params->get('languageFilter'));
        $helper->setOrderBy($params->get('ordering'), $params->get('orderingdirection'));
        $helper->setLimit($limitForQuery);
        if (0 < count($tags)) {
            $helper->filterByTags($tags);
        }
    } else {
        $helper->type = 'category';
        $helper->setSelectState('cat.published', $params->get('statecategory'));
        $helper->setLanguage('cat.language', $params->get('languageFilter'));
        $helper->setOrderBy($params->get('orderingcategory'), $params->get('orderingcategorydirection'));
        $helper->setLimit($params->get('countcategory'));
    }

    switch ($todo) {
        // article:: current article
        case 11:
            $id = $helper->getCurrentArticle('id');
            $arrItems = is_numeric($id) ? $helper->getArticle($id) : [];

            break;

        // article:: via Get/Post
        case 12:
            $id = $input->get('qlcontent');
            $arrItems = $helper->getArticle($id);

            break;

        // article:: current article; if none, fix category
        case 13:
            $id = $helper->getCurrentArticle('id');
            if (empty($id)) {
                // set vars for use of display below
                $todo = 45;
                $helper->type = 'category';
                // repeated statement for category display
                $helper->resetQuery();
                $helper->setSelectState('cat.published', $params->get('statecategory', 0));
                $helper->setFeatured('cat.featured', 0);
                $helper->setLanguage('cat.language', $params->get('language'));
                $helper->setOrderBy($params->get('orderingcategory'), $params->get('orderingcategorydirection'));
                $helper->setLimit($params->get('countcategory'));

                $id = $helper->getCurrentArticle('id');
                if (!empty($id)) {
                    $arrItems = $helper->getArticle($id);
                    $helper->type = 'article';
                } else {
                    $catid = [0 => $helper->getCurrentCategory('id')];
                    if (empty($catid[0])) {
                        $catid = $params->get('categoryid');
                    }
                    $arrItems = $helper->getCategory($catid);
                }
            } else {
                $arrItems = $helper->getArticle($id);
            }

            break;

        // article:: fix article
        case 15:
            $id = $params->get('articleid', 0);
            $arrItems = $helper->getArticle($id);

            break;

        // article:: current article; if none, show fix article
        case 17:
            $id = $helper->getCurrentArticle('id');
            if (empty($id)) {
                $id = $params->get('articleid');
            }
            $arrItems = $helper->getArticle($id);

            break;

        // articles:: articles of fix category
        case 19:
            $arrCatid = $params->get('categoryid', 0);
            if (!is_array($arrCatid)) {
                $arrCatid = [$arrCatid];
            }
            if (0 >= count($arrCatid)) {
                $isCategoryView = false;
            } else {
                $arrItems = $helper->getArticles($arrCatid);
            }

            break;

        // articles:: articles of current category
        case 21:
            $objInput = Factory::getApplication()->input;
            $strOption = $objInput->get('option');
            $strView = $objInput->get('view');
            if ('com_content' === $strOption && 'article' === $strView) {
                $catid = [0 => $helper->getCurrentArticle('catid')];
            } elseif ('com_content' === $strOption && 'category' === $strView) {
                $catid = [0 => $helper->getCurrentCategory('id')];
            }
            if (is_countable($catid) && count($catid) > 0) {
                $arrItems = $helper->getArticles($catid);
            } else {
                $isCategoryView = false;
            }

            break;

        // articles:: articles of fix category if no current category
        case 23:
            $catid = [0 => $helper->getCurrentArticle('catid')];
            if (empty($catid[0])) {
                $catid = [0 => $helper->getCurrentCategory('id')];
            }
            if (empty($catid[0])) {
                $catid = [0 => $params->get('categoryid')];
            }
            if (0 < count($catid)) {
                $arrItems = $helper->getArticles($catid);
            } else {
                $isCategoryView = false;
            }

            break;

        // category:: current category of article(s)
        case 41:
            $arrCatid = [0 => $helper->getCurrentArticle('catid')];
            $arrCatid = array_filter($arrCatid);
            if ($arrCatid === []) {
                $arrCatid = [0 => $helper->getCurrentCategory('id')];
            }
            $arrCatid = array_filter($arrCatid);
            if (0 < count($arrCatid)) {
                $arrItems = $helper->getCategory($arrCatid);
            } else {
                $isCategoryView = false;
            }

            break;

        // category:: fix category
        case 43:
            $catid = $params->get('categoryid', 0);
            if (count($catid) > 0) {
                $arrItems = $helper->getCategory($catid);
            } else {
                $isCategoryView = false;
            }

            break;

        // category:: fix category if no current article || category
        case 45:
            $id = $helper->getCurrentArticle('id');
            if ('' != $id) {
                $arrItems = $helper->getArticle($id);
                $helper->type = 'article';
            } else {
                $catid = [0 => $helper->getCurrentCategory('id')];
                if ('' == $catid[0]) {
                    $catid = $params->get('categoryid');
                }
                $arrItems = $helper->getCategory($catid);
            }

            break;

        // category:: list categories of fix parent category
        case 47:
            $catid = $params->get('categoryid', 0);
            $ids = $helper->getCategoryChildren($catid);
            foreach ($ids as $v) {
                $arr_id[] = $v->id;
            }
            if (isset($arr_id)) {
                $arrItems = $helper->getCategory($arr_id);
            }
            $helper->type = 'category';

            break;

        // category:: list child categories of current parent category
        case 49:
            $catid = [0 => $helper->getCurrentArticle('catid')];
            if ('' == $catid[0]) {
                $catid = [0 => $helper->getCurrentCategory('id')];
            }
            $ids = $helper->getCategoryChildren($catid);
            foreach ($ids as $v) {
                $arr_id[] = $v->id;
            }
            if (isset($arr_id) && count($arr_id) > 0) {
                $arrItems = $helper->getCategory($arr_id);
            }
            $helper->type = 'category';

            break;

        // category:: list child categories of current parent category - if no current, list fix category
        case 50:
            $catid = [0 => $helper->getCurrentArticle('catid')];
            if (empty($catid[0])) {
                $catid = [0 => $helper->getCurrentCategory('id')];
            }
            if (empty($catid[0])) {
                $catid = $params->get('categoryid');
            }
            $ids = $helper->getCategoryChildren($catid);
            foreach ($ids as $v) {
                $arr_id[] = $v->id;
            }
            if (isset($arr_id) && count($arr_id) > 0) {
                $arrItems = $helper->getCategory($arr_id);
            }
            $helper->type = 'category';

            break;

        // category:: list child categories of current parent category - if no current, list fix category - if no children categories, show articles
        case 51:
            $catid = [0 => $helper->getCurrentArticle('catid')];
            if (empty($catid[0])) {
                $catid = [0 => $helper->getCurrentCategory('id')];
            }
            if (empty($catid[0])) {
                $catid = $params->get('categoryid');
            }
            $ids = $helper->getCategoryChildren($catid);
            if (count($ids) > 0) {
                foreach ($ids as $v) {
                    $arr_id[] = $v->id;
                }
                if (isset($arr_id) && count($arr_id) > 0) {
                    $arrItems = $helper->getCategory($arr_id);
                }
                $helper->type = 'category';
            } else {
                $helper->setSelectState('con.state', $params->get('state'));
                $helper->setFeatured('con.featured', $params->get('featured'));
                $helper->setLanguage('con.language', $params->get('languageFilter'));
                $helper->setOrderBy($params->get('ordering'), $params->get('orderingdirection'));
                $helper->setLimit($limit);
                $arrItems = $helper->getArticles($catid);
                $helper->type = 'article';
            }
    }

    if (!$isCategoryView) {
        echo Text::_('MOD_QLCONTENT_NOCATEGORYDEFINED');
    }

// Add default values to item where data is empty
    if ($params->get('default_use', 0)) {
        $itemDefault = new stdClass();
        if ($todo < 40) {
            for ($i = 1; $i <= 20; ++$i) {
                if (false != $params->get('art_position' . $i)) {
                    $itemDefault->{$params->get('art_position' . $i)} = $params->get('default_art_position' . $i);
                }
            }
        } else {
            for ($i = 1; $i <= 8; ++$i) {
                if (false != $params->get('cat_position' . $i)) {
                    $itemDefault->{$params->get('cat_position' . $i)} = $params->get('default_cat_position' . $i);
                }
            }
        }
        if (0 === count($arrItems)) {
            $arrItems[0] = $itemDefault;
        } elseif (is_array($arrItems)) {
            foreach ($arrItems as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    if ('' == $v2 && isset($itemDefault->{$k2})) {
                        $arrItems[$k]->{$k2} = $itemDefault->{$k2};
                    }
                }
            }
        }
    }

    if (isset($arrItems) && is_array($arrItems)) {
        $arrItemsOrdered = [];
        $dataOfItems = [];
        foreach ($arrItems as $k => $v) {
            if (!isset($v->id)) {
                continue;
            }
            $dataOfItems[$v->id] = new stdClass();
            for ($i = 1; $i <= 20; ++$i) {
                if (!isset($v->id)) {
                    $v->id = 0;
                }

                $field = '';
                if ('category' === $helper->type) {
                    $field = $params->get('cat_position' . $i);
                } elseif ('article' === $helper->type) {
                    $field = $params->get('art_position' . $i);
                }
                if ('0' != $field && !empty($field) && isset($v->{$field}) && ((!empty($v->{$field}) && $params->get('displayempty')) || !$params->get('displayempty'))) {
                    if (!isset($arrItemsOrdered[$k])) {
                        $arrItemsOrdered[$k] = new stdClass();
                    }
                    $arrItemsOrdered[$k]->{$field} = $v->{$field};
                } elseif ('edit' == $field) {
                    if (!isset($arrItemsOrdered[$k])) {
                        $arrItemsOrdered[$k] = new stdClass();
                    }
                    $arrItemsOrdered[$k]->{$field} = 1;
                    $dataOfItems[$v->id]->{$field} = 1;
                } elseif ('readmore' == $field) {
                    if (!isset($arrItemsOrdered[$k])) {
                        $arrItemsOrdered[$k] = new stdClass();
                    }
                    $arrItemsOrdered[$k]->{$field} = $helper->readmoreBehavior($v, $params);
                    $dataOfItems[$v->id]->{$field} = $helper->readmoreBehavior($dataOfItems[$v->id], $params);
                }
                unset($field);
            }
            $arrItemsOrdered[$k]->id = $v->id;
            $dataOfItems[$v->id] = $arrItems[$k];
            if ($params->get('striptags', false) && isset($arrItemsOrdered[$k]->introtext)) {
                $arrItemsOrdered[$k]->introtext = strip_tags((string) $dataOfItems[$v->id]->introtext);
            }
            if (0 < (int)$params->get('cutintrotext', 0) && isset($arrItemsOrdered[$k]->introtext)) {
                $arrItemsOrdered[$k]->introtext = $helper->cutString($arrItemsOrdered[$k]->introtext, $params->get('cutintrotext', '0'), $params->get('cutintrotextunit', 'chars'));
            }
            if ($params->get('catstriptags', false) && isset($arrItemsOrdered[$k]->description)) {
                $arrItemsOrdered[$k]->description = strip_tags((string) $dataOfItems[$v->id]->description);
            }
            if (0 < (int)$params->get('catcutintrotext', 0) && isset($arrItemsOrdered[$k]->description)) {
                $boolCut = (strlen($arrItemsOrdered[$k]->description) > $params->get('catcutintrotextunit', 'chars'));
                $arrItemsOrdered[$k]->description = $helper->cutString($arrItemsOrdered[$k]->description, $params->get('catcutintrotext', '0'), $params->get('catcutintrotextunit', 'chars'));
                if ($boolCut) {
                    $arrItemsOrdered[$k]->description .= '...';
                }
            }
            $dataOfItems[$v->id] = $helper->urlBehavior($dataOfItems[$v->id]);
            $dataOfItems[$v->id] = $helper->imageBehavior($dataOfItems[$v->id], 'image_intro');
            $dataOfItems[$v->id] = $helper->imageBehavior($dataOfItems[$v->id], 'image_fulltext');
            if ($params->get('prepareContent', 0)) {
                $dataOfItems[$v->id] = $helper->prepareContent($dataOfItems[$v->id]);
                $arrItemsOrdered[$k] = $helper->prepareContent($arrItemsOrdered[$k]);
            }
        }
    }

    if (modQlcontentHelper::FEATURED_PREFERRED === $featured) {
        $arrItemsIdFeatured = array_filter($dataOfItems, fn($item) => (bool)($item->featured ?? false));
        array_walk($arrItemsIdFeatured, function (&$item) {
            $item = $item->id;
        });
        $arrItemsIdNormalo = array_filter($dataOfItems, fn($item) => !($item->featured ?? false));
        array_walk($arrItemsIdNormalo, function (&$item) {
            $item = $item->id;
        });
        $arrItemsOrdered = [
            ...array_filter($arrItemsOrdered, fn($item) => in_array($item->id, $arrItemsIdFeatured)),
            ...array_filter($arrItemsOrdered, fn($item) => in_array($item->id, $arrItemsIdNormalo)),
        ];
        $arrItemsOrdered = array_slice($arrItemsOrdered, 0, $limit);
    }

    if (isset($arrItemsOrdered) && is_array($arrItemsOrdered) && 0 < count($arrItems)) {
        $itemCount = count($arrItemsOrdered);
        if (1 < $itemCount && 1 == $params->get('pagination')) {
            $arrItemsOrdered = $helper->addPagination($arrItemsOrdered, $params);
            $pagination = $helper->pagination;
        }
        $moduleclass_sfx = empty($params->get('moduleclass_sfx'))
            ? ''
            : htmlspecialchars((string) $params->get('moduleclass_sfx'));

        require ModuleHelper::getLayoutPath('mod_qlcontent', $params->get('layout', 'default'));
    } elseif ($params->get('boolEmptyMessage', 0) && isset($arrItemsOrdered) && is_array($arrItemsOrdered) && 0 === count($arrItems)) {
        require ModuleHelper::getLayoutPath('mod_qlcontent', $params->get('layout', 'default') . '_message');
    }

    // echo '<pre>';print_r($dataOfItems);die;
    unset($arrItemsPerRow, $intPerRow);
} catch (Error|Exception $e) {
    $errors->setError($e->getMessage());
}

if ($errors->hasErrors() && 0 === count($arrItems)) {
    require ModuleHelper::getLayoutPath('mod_qlcontent', $params->get('layout', 'default') . '_error');
}
