<?php
/**
 * @package        mod_qlcontent
 * @copyright    Copyright (C) 2022 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 || later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') || die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
$obj_helper = new modQlcontentHelper($module);

$numId = '';
$numTodo = $params->get('todo');
$boolCategory = true;

$numModuleIdCheck = 0;
if ($numModuleIdCheck === $module->id) {
    print_r($numTodo);
    die;
}


if (0 != $params->get('checkToken', 0) && false == JSession::checkToken($params->get('checkToken', 'post'))) {
    return;
}
if (39 >= $numTodo) {
    $obj_helper->type = 'article';
    $obj_helper->setSelectState('con.state', $params->get('state'), 1);
    $obj_helper->setFeatured('con.featured', $params->get('featured'), 0);
    $obj_helper->setLanguage('con.language', $params->get('languageFilter'));
    $obj_helper->setOrderBy($params->get('ordering'), $params->get('orderingdirection'));
    $obj_helper->setLimit($params->get('count'));
} else {
    $obj_helper->type = 'category';
    $obj_helper->setSelectState('cat.published', $params->get('statecategory'));
    //$obj_helper->setFeatured('cat.featured',0);
    $obj_helper->setLanguage('cat.language', $params->get('languageFilter'));
    $obj_helper->setOrderBy($params->get('orderingcategory'), $params->get('orderingcategorydirection'));
    $obj_helper->setLimit($params->get('countcategory'));
}

switch ($numTodo) {
    /*article:: current article*/
    case 11:
        $numId = $obj_helper->getCurrentArticle('id');
        $arrItems = $obj_helper->getArticle($numId);
        break;

    /*article:: via Get/Post*/
    case 12:
        $input = JFactory::getApplication()->input;
        $numId = $input->get('qlcontent');
        $arrItems = $obj_helper->getArticle($numId);
        //echo '<pre>';print_r($arrItems);die;
        break;

    /*article:: current article; if none, fix category*/
    case 13:
        $numId = $obj_helper->getCurrentArticle('id');
        if ('' == $numId) {
            /*set vars for use of display below*/
            $numTodo = 45;
            $obj_helper->type = 'category';
            /*repeated statement for category display*/
            $obj_helper->resetQuery();
            $obj_helper->setSelectState('cat.published', $params->get('statecategory'));
            $obj_helper->setFeatured('cat.featured', 0);
            $obj_helper->setLanguage('cat.language', $params->get('language'));
            $obj_helper->setOrderBy($params->get('orderingcategory'), $params->get('orderingcategorydirection'));
            $obj_helper->setLimit($params->get('countcategory'));
        } else {
            $arrItems = $obj_helper->getArticle($numId);
        }
        break;

    /*article:: fix article*/
    case 15:
        $numId = $params->get('articleid');
        $arrItems = $obj_helper->getArticle($numId);
        break;

    /*article:: current article; if none, show fix article*/
    case 17:
        $numId = $obj_helper->getCurrentArticle('id');
        if ('' == $numId) {
            $numId = $params->get('articleid');
        }
        $arrItems = $obj_helper->getArticle($numId);
        break;

    /*articles:: articles of fix category*/
    case 19:
        $arrCatid = $params->get('categoryid');
        if(!is_array($arrCatid)) {
            $arrCatid = [$arrCatid];
        }
        if (0 >= count($arrCatid)) {
            $boolCategory = false;
        } else {
            $arrItems = $obj_helper->getArticles($arrCatid);
        }
        break;

    /*articles:: articles of current category*/
    case 21:
        $catid = $obj_helper->getCurrentArticle('catid');
        if (count($catid) > 0) $arrItems = $obj_helper->getArticles([0 => $catid]);
        else $boolCategory = false;
        break;

    /*articles:: articles of fix category if no current category*/
    case 23:
        $catid = [0 => $obj_helper->getCurrentArticle('catid')];
        if (0 == $catid[0]) $catid = [0 => $obj_helper->getCurrentCategory("id")];
        if (0 == $catid[0]) $catid = $params->get('categoryid');
        if (0 < count($catid[0])) {
            $arrItems = $obj_helper->getArticles($catid);
        }
        else $boolCategory = false;
        break;

    /*category:: current category of article(s)*/
    case 41:
        $arrCatid = $obj_helper->getCurrentArticle('catid');
		if (is_array($arrCatid) && count($arrCatid) <= 0 || '' == $arrCatid[0]) {
            $arrCatid = [0 => $obj_helper->getCurrentCategory('id')];
        }
		if (0 < count($arrCatid)) {
            $arrItems = $obj_helper->getCategory($arrCatid);
        } else {
            $boolCategory = false;
        }
        break;

    /*category:: fix category*/
    case 43:
        $catid = $params->get('categoryid');
        if (count($catid) > 0) {
            $arrItems = $obj_helper->getCategory($catid);
        } else {
            $boolCategory = false;
        }
        break;

    /*category:: fix category if no current article || category*/
    case 45:
        $numId = $obj_helper->getCurrentArticle('id');
        if ('' != $numId) {
            $arrItems = $obj_helper->getArticle($numId);
            $obj_helper->type = 'article';
        } else {
            $catid = [0 => $obj_helper->getCurrentCategory('id')];
            if (count($catid) <= 0 || '' == $catid[0]) $catid = $params->get('categoryid');
            //print_r($catid);die;
            $arrItems = $obj_helper->getCategory($catid);
        }
        break;

    /*category:: list categories of fix parent category*/
    case 47:
        $catid = $params->get('categoryid');
        $numIds = $obj_helper->getCategoryChildren($catid);
        foreach ($numIds as $k => $v) $arr_id[] = $v->id;
        if (isset($arr_id)) $arrItems = $obj_helper->getCategory($arr_id);
        $obj_helper->type = 'category';
        break;

    /*category:: list child categories of current parent category*/
    case 49:
        $catid = [0 => $obj_helper->getCurrentArticle('catid')];
        if ('' == $catid[0]) $catid = [0 => $obj_helper->getCurrentCategory('id')];
        $numIds = $obj_helper->getCategoryChildren($catid);
        if (isset($numIds) && is_array($numIds) && count($numIds) > 0) foreach ($numIds as $k => $v) $arr_id[] = $v->id;
        if (isset($arr_id) && count($arr_id) > 0) $arrItems = $obj_helper->getCategory($arr_id);
        $obj_helper->type = 'category';
        break;

    /*category:: list child categories of current parent category - if no current, list fix category*/
    case 50:
        $catid = [0 => $obj_helper->getCurrentArticle('catid')];
        if ('' == $catid[0]) $catid = [0 => $obj_helper->getCurrentCategory('id')];
        if ('' == $catid[0]) $catid = $params->get('categoryid');
        $numIds = $obj_helper->getCategoryChildren($catid);
        if (isset($numIds) && is_array($numIds) && count($numIds) > 0) foreach ($numIds as $k => $v) $arr_id[] = $v->id;
        if (isset($arr_id) && count($arr_id) > 0) $arrItems = $obj_helper->getCategory($arr_id);
        $obj_helper->type = 'category';
        break;
    /*category:: list child categories of current parent category - if no current, list fix category - if no children categories, show articles*/
    case 51:
        $catid = [0 => $obj_helper->getCurrentArticle('catid')];
        if ('' == $catid[0]) $catid = [0 => $obj_helper->getCurrentCategory('id')];
        if ('' == $catid[0]) $catid = $params->get('categoryid');
        $numIds = $obj_helper->getCategoryChildren($catid);
        if (isset($numIds) && is_array($numIds) && count($numIds) > 0) {
            foreach ($numIds as $k => $v) $arr_id[] = $v->id;
            if (isset($arr_id) && count($arr_id) > 0) $arrItems = $obj_helper->getCategory($arr_id);
            $obj_helper->type = 'category';
        } else {
            $obj_helper->setSelectState('con.state', $params->get('state'));
            $obj_helper->setFeatured('con.featured', $params->get('featured'));
            $obj_helper->setLanguage('con.language', $params->get('languageFilter'));
            $obj_helper->setOrderBy($params->get('ordering'), $params->get('orderingdirection'));
            $obj_helper->setLimit($params->get('count'));
            $arrItems = $obj_helper->getArticles($catid);
            $obj_helper->type = 'article';
        }
}

if (false === $boolCategory) {
    echo(JText::_('MOD_QLCONTENT_NOCATEGORYDEFINED'));
}


/*Add default values to item where data is empty*/
if (1 == $params->get('default_use')) {
    $itemDefault = new stdClass();
	if ($numTodo < 40) {
        for ($i = 1; $i <= 20; $i++) {
            if (false != $params->get('art_position' . $i)) {
                $itemDefault->{$params->get('art_position' . $i)} = $params->get('default_art_position' . $i);
            }
        }
    } elseif ($numTodo >= 40) {
        for ($i = 1; $i <= 8; $i++) {
            if (false != $params->get('cat_position' . $i)) {
                $itemDefault->{$params->get('cat_position' . $i)} = $params->get('default_cat_position' . $i);
            }
        }
    }
    if (0 == count($arrItems)) {
        $arrItems[0] = $itemDefault;
    } elseif (is_array($arrItems)) {
        foreach ($arrItems as $k => $v) foreach ($v as $k2 => $v2) if ('' == $v2 && isset($itemDefault->$k2)) $arrItems[$k]->$k2 = $itemDefault->$k2;
    }
}


if (isset($arrItems) && is_array($arrItems)) {
    $arrItemsOrdered = [];
    $dataOfItems = [];
    foreach ($arrItems as $k => $v) {
        if (!isset($v->id)) continue;
        $dataOfItems[$v->id] = new stdClass();
        for ($i = 1; $i <= 20; $i++) {
            if (!isset($v->id)) $v->id = 0;


            if ('category' == $obj_helper->type) $strField = $params->get('cat_position' . $i);
            elseif ('article' == $obj_helper->type) $strField = $params->get('art_position' . $i);
            if ('0' != $strField && '' != $strField && isset($v->$strField) && (('' != $v->$strField && 1 == $params->get('displayempty')) || 0 == $params->get('displayempty'))) {
                if (!isset($arrItemsOrdered[$k])) {
                    $arrItemsOrdered[$k] = new stdClass;
                }
                $arrItemsOrdered[$k]->$strField = $v->$strField;
                //$dataOfItems[$v->id]->$strField=$v->$strField;
                //echo '<pre>';print_r($dataOfItems);

            } elseif ('edit' == $strField) {
                if (!isset($arrItemsOrdered[$k])) {
                    $arrItemsOrdered[$k] = new stdClass;
                }
                $arrItemsOrdered[$k]->$strField = 1;
                $dataOfItems[$v->id]->$strField = 1;
            } elseif ('readmore' == $strField) {
                if (!isset($arrItemsOrdered[$k])) {
                    $arrItemsOrdered[$k] = new stdClass;
                }
                $arrItemsOrdered[$k]->$strField = $obj_helper->readmoreBehavior($v, $params);
                $dataOfItems[$v->id]->$strField = $obj_helper->readmoreBehavior($dataOfItems[$v->id], $params);
            }
            unset($strField);
        }
        $arrItemsOrdered[$k]->id = $v->id;
        $dataOfItems[$v->id] = $arrItems[$k];
        if (1 == $params->get('striptags', '0') && isset($arrItemsOrdered[$k]->introtext)) {
            $arrItemsOrdered[$k]->introtext = strip_tags($dataOfItems[$v->id]->introtext);
        }
        if (0 < $params->get('cutintrotext', '0') && isset($arrItemsOrdered[$k]->introtext)) {
            $arrItemsOrdered[$k]->introtext = $obj_helper->cutString($arrItemsOrdered[$k]->introtext, $params->get('cutintrotext', '0'), $params->get('cutintrotextunit', 'chars'));
        }
        if (1 == $params->get('catstriptags', '0') && isset($arrItemsOrdered[$k]->description)) {
            $arrItemsOrdered[$k]->description = strip_tags($dataOfItems[$v->id]->description);
        }
        if (0 < $params->get('catcutintrotext', '0') && isset($arrItemsOrdered[$k]->description)) {
            $boolCut = false;
            if (strlen($arrItemsOrdered[$k]->description)>$params->get('catcutintrotextunit', 'chars')) {
                $boolCut = true;
            }
            $arrItemsOrdered[$k]->description = $obj_helper->cutString($arrItemsOrdered[$k]->description, $params->get('catcutintrotext', '0'), $params->get('catcutintrotextunit', 'chars'));
            if ($boolCut) $arrItemsOrdered[$k]->description .= '...';
        }
        $dataOfItems[$v->id] = $obj_helper->urlBehavior($dataOfItems[$v->id]);
        $dataOfItems[$v->id] = $obj_helper->imageBehavior($dataOfItems[$v->id], 'image_intro');
        $dataOfItems[$v->id] = $obj_helper->imageBehavior($dataOfItems[$v->id], 'image_fulltext');
        if (1 == $params->get('prepareContent', 0)) {
            $dataOfItems[$v->id] = $obj_helper->prepareContent($dataOfItems[$v->id]);
            $arrItemsOrdered[$k] = $obj_helper->prepareContent($arrItemsOrdered[$k]);
        }
        //echo '<pre>';print_r($dataOfItems);die;
    }
}

if (isset($arrItemsOrdered) && is_array($arrItemsOrdered) && 0 < count($arrItems)) {
    $itemCount = count($arrItemsOrdered);
    if (1 < $itemCount && 1 == $params->get('pagination')) {
        $arrItemsOrdered = $obj_helper->addPagination($arrItemsOrdered, $params);
        $pagination = $obj_helper->pagination;
    }
    $moduleclass_sfx = !empty($params->get('moduleclass_sfx')) ? htmlspecialchars($params->get('moduleclass_sfx')) : '';
    require JModuleHelper::getLayoutPath('mod_qlcontent', $params->get('layout', 'default'));
} elseif(1 == $params->get('boolEmptyMessage', 0) && isset($arrItemsOrdered) && is_array($arrItemsOrdered) && 0 === count($arrItems)) {
    require JModuleHelper::getLayoutPath('mod_qlcontent', $params->get('layout', 'default') . '_message');
}

//echo '<pre>';print_r($dataOfItems);die;
unset($arrItemsPerRow);
unset($intPerRow);
