<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']): 'index';
if($op == 'index') {
	$id = intval($_GPC['id']);
	if(empty($id)) {
		imessage('页面id不能为空', imurl('wmall/home/index'), 'info');
	}
	$page = get_diypage_diy($id, true);
	if(empty($id)) {
		imessage('页面不能为空', imurl('wmall/home/index'), 'info');
	}
	$_W['page']['title'] = $page['data']['page']['title'];
	$_share = get_diypage_share($page);

	$diypage = $page['data']['page'];
	$diyitems = $page['data']['items'];

//	p($diyitems);
//	die;
}
include itemplate('diy');