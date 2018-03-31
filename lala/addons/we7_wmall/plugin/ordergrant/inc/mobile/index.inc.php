<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '下单有礼';
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');
$grantType = '积分';

if ($config_ordergrant['grantType'] == 'credit2') {
	$grantType = '元';
}

if ($op == 'index') {
	$year = date('Y');
	$month = date('m');
	$calendar = getCalendar($year, $month, true);
	$condition = ' where uniacid = :uniacid and uid = :uid';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']);

	if ($config_ordergrant['cycle'] == 1) {
		$condition .= ' and stat_month = :stat_month';
		$params[':stat_month'] = date('Ym');
	}

	$continuous_get = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_grant_record') . $condition . ' and type = 1', $params, 'days');
	$all_get = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_grant_record') . $condition . ' and type = 2', $params, 'days');
}

if ($op == 'get') {
	if ($_W['ispost']) {
		$days = intval($_GPC['days']);
		$type = intval($_GPC['type']);
		$ordergrant = ordergrant_grant_record($days, $type, $_W['member']['uid']);
		imessage($ordergrant, '', 'ajax');
	}
}

if ($op == 'next') {
	$difference = intval($_GPC['difference']);
	$grant = ordergrant_next_grant($difference);
	imessage(error(0, $grant['message']), '', 'ajax');
}

$_share = array('title' => $config_ordergrant['ordergrant_share']['title'], 'desc' => $config_ordergrant['ordergrant_share']['desc'], 'link' => imurl('ordergrant/index', array(), true), 'imgUrl' => tomedia($config_ordergrant['ordergrant_share']['imgUrl']));
include itemplate('index');

?>
