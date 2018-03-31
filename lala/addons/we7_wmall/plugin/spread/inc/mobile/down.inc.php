<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$_W['page']['title'] = '我的团队';
$basic = get_plugin_config('spread.basic');
if($op == 'index') {
	$condition = " where uniacid = :uniacid";
	$params = array(
		':uniacid' => $_W['uniacid'],
	);
	$spreadid = $_GPC['spreadid'] ? trim($_GPC['spreadid']) : 'spread1';
	if($spreadid) {
		$condition .= " and {$spreadid} = :spreadid";
		$params[':spreadid'] = $_W['member']['uid'];
	}

	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= ' and id < :id';
		$params[':id'] = trim($_GPC['min']);
	}
	$members = pdo_fetchall('select * from'. tablename('tiny_wmall_members'). $condition. ' order by id desc limit 10', $params, 'id');
	$min = 0;
	if(!empty($members)) {
		foreach($members as &$value) {
			$price = pdo_fetchcolumn('select sum(final_fee) from'. tablename('tiny_wmall_order') . "where uniacid = :uniacid and uid = :uid and {$spreadid} = :spreadid and status = 5", array(':uniacid' => $_W['uniacid'], ':uid' => $value['uid'], ':spreadid' => $_W['member']['uid']));
			$frquency = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_order') . "where uniacid = :uniacid and uid = :uid and {$spreadid} = :spreadid and status = 5", array(':uniacid' => $_W['uniacid'], ':uid' => $value['uid'], ':spreadid' => $_W['member']['uid']));
			$value['price'] = round($price, 2);
			$value['frquency'] = intval($frquency);
			$value['addtime'] = date('Y-m-d', $value['addtime']);
		}
		$min = min(array_keys($members));
	}
	if($_W['ispost']) {
		$members = array_values($members);
		$respon = array('errno' => 0, 'message' => $members, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
	$level1 = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members'). 'where uniacid = :uniacid and spread1 = :spread1', array(':uniacid' => $_W['uniacid'], ':spread1' => $_W['member']['uid']));
	$level2 = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members'). 'where uniacid = :uniacid and spread2 = :spread2', array(':uniacid' => $_W['uniacid'], ':spread2' => $_W['member']['uid']));
}


include itemplate('down');