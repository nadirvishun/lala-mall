<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('cover');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$routers = array(
	'index' => array(
		'title' => '入口设置',
		'url' => imurl('spread/index', array(), true),
		'do' => 'index',
	),
);
$router = $routers[$op];
$_W['page']['title'] = $router['title'];

if($_W['ispost']) {
	$keyword = trim($_GPC['keyword']) ? trim($_GPC['keyword']) : imessage(error(-1, '关键词不能为空'), '', 'ajax');
	$cover = array(
		'keyword' => trim($_GPC['keyword']),
		'title' => trim($_GPC['title']),
		'thumb' => trim($_GPC['thumb']),
		'description' => trim($_GPC['description']),
		'status' => intval($_GPC['status']),
		'do' => $router['do'],
		'url' => $router['url'],
	);
	cover_build($cover);
	imessage(error(0, '设置封面成功'), referer(), 'ajax');
}
$cover = cover_fetch(array('do' => $router['do']));
$cover = array_merge($cover, $router);
include itemplate('cover');