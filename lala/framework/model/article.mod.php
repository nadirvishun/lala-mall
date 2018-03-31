<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

function article_categorys($type = 'news') {
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('article_category') . ' WHERE type = :type ORDER BY displayorder DESC', array(':type' => $type), 'id');
	return $categorys;
}

function article_news_info($id) {
	$id = intval($id);
	$news = pdo_fetch('SELECT * FROM ' . tablename('article_news') . ' WHERE id = :id', array(':id' => $id));
	if(empty($news)) {
		return error(-1, '新闻不存在或已经删除');
	}else {
		pdo_update('article_news',array('click' => $news['click']+1),array('id' => $id));
	}
	return $news;
}

function article_notice_info($id) {
	$id = intval($id);
	$news = pdo_fetch('SELECT * FROM ' . tablename('article_notice') . ' WHERE id = :id', array(':id' => $id));
	if(empty($news)) {
		return error(-1, '公告不存在或已经删除');
	}
	return $news;
}

function article_news_home($limit = 5) {
	$limit = intval($limit);
	$news = pdo_fetchall('SELECT * FROM ' . tablename('article_news') . ' WHERE is_display = 1 AND is_show_home = 1 ORDER BY displayorder DESC,id DESC LIMIT ' . $limit, array(), 'id');
	return $news;
}

function article_notice_home($limit = 5) {
	$limit = intval($limit);
	$notice = pdo_fetchall('SELECT * FROM ' . tablename('article_notice') . ' WHERE is_display = 1 AND is_show_home = 1 ORDER BY displayorder DESC,id DESC LIMIT ' . $limit, array(), 'id');
	foreach ($notice as $key => $notice_val) {
		$notice[$key]['style'] = iunserializer($notice_val['style']);
	}
	return $notice;
}

function article_news_all($filter = array(), $pindex = 1, $psize = 10) {
	$condition = ' WHERE is_display = 1';
	$params = array();
	if(!empty($filter['title'])) {
		$condition .= ' AND titie LIKE :title';
		$params[':title'] = "%{$filter['title']}%";
	}
	if($filter['cateid'] > 0) {
		$condition .= ' AND cateid = :cateid';
		$params[':cateid'] = $filter['cateid'];
	}
	$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('article_news') . $condition, $params);
	$news = pdo_fetchall('SELECT * FROM ' . tablename('article_news') . $condition . ' ORDER BY displayorder DESC ' . $limit, $params, 'id');
	return array('total' => $total, 'news' => $news);
}

function article_notice_all($filter = array(), $pindex = 1, $psize = 10) {
	$condition = ' WHERE is_display = 1';
	$params = array();
	if(!empty($filter['title'])) {
		$condition .= ' AND titie LIKE :title';
		$params[':title'] = "%{$filter['title']}%";
	}
	if($filter['cateid'] > 0) {
		$condition .= ' AND cateid = :cateid';
		$params[':cateid'] = $filter['cateid'];
	}
	$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('article_notice') . $condition, $params);
	$notice = pdo_fetchall('SELECT * FROM ' . tablename('article_notice') . $condition . ' ORDER BY displayorder DESC ' . $limit, $params, 'id');
	foreach ($notice as $key => $notice_val) {
		$notice[$key]['style'] = iunserializer($notice_val['style']);
	}
	return array('total' => $total, 'notice' => $notice);
}


function article_category_delete($id) {
	$id = intval($id);
	if (empty($id)) {
		return false;
	}
	load()->func('file');
	$category = pdo_fetch("SELECT id, parentid, nid FROM " . tablename('site_category')." WHERE id = " . $id);
	if (empty($category)) {
		return false;
	}
	if ($category['parentid'] == 0) {
		$children_cates = pdo_getall('site_category', array('parentid' => $id));
		pdo_update('site_article', array('pcate' => 0), array('pcate' => $id));
		if (!empty($children_cates)) {
			$children_cates_id = array_column($children_cates, 'id');
			pdo_update('site_article', array('ccate' => 0), array('ccate' => $children_cates_id), 'OR');
		}
	} else {
		pdo_update('site_article', array('ccate' => 0), array('ccate' => $id));
	}
	$navs = pdo_fetchall("SELECT icon, id FROM ".tablename('site_nav')." WHERE id IN (SELECT nid FROM ".tablename('site_category')." WHERE id = {$id} OR parentid = '$id')", array(), 'id');
	if (!empty($navs)) {
		foreach ($navs as $row) {
			file_delete($row['icon']);
		}
		pdo_query("DELETE FROM ".tablename('site_nav')." WHERE id IN (".implode(',', array_keys($navs)).")");
	}
	pdo_delete('site_category', array('id' => $id, 'parentid' => $id), 'OR');
	return true;
}


