<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$accounts = pdo_fetchall('SELECT a.*, b.title FROM ' . tablename('tiny_wmall_store_account') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where uniacid = :uniacid ORDER BY a.amount DESC', array(':uniacid' => $_W['uniacid']));
	if(!empty($accounts)) {
		foreach($accounts as &$row) {
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$delivery_modes = store_delivery_modes();
}

include itemplate('merchant/data');



