<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');
$_W['page']['title'] = '随意购';

if ($op == 'index') {
	$categorys = pdo_fetchall('select * from ' . tablename('tiny_wmall_errander_category') . ' where uniacid = :uniacid and agentid = :agentid and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
	$orders = pdo_fetchall('select a.*,b.title,b.thumb from ' . tablename('tiny_wmall_errander_order') . ' as a left join ' . tablename('tiny_wmall_errander_category') . ' as b on a.order_cid = b.id where a.uniacid = :uniacid and a.agentid = :agentid order by a.id desc limit 5', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
	$delivery_num = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_deliveryer') . ' where uniacid = :uniacid and agentid = :agentid and sid = 0', array(':uniacid' => $_W['uniacid'], ':agentid' => $_W['agentid']));
}

if ($op == 'deliveryer') {
	mload()->model('deliveryer');
	$deliveryer = deliveryer_fetchall();
	imessage(error(0, $deliveryer), '', 'ajax');
}

include itemplate('index');

?>
