<?php
/**
 * 外送系�
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('store');
global $_W, $_GPC;
$_W['page']['title'] = '商户中心';
$sid = intval($_GPC['__mg_sid']);
$store = $_W['we7_wmall']['store'];
$condition = ' where uniacid = :uniacid and sid = :sid and status = 5 and is_pay = 1 and stat_day = :stat_day';
$params = array(
	':uniacid' => $_W['uniacid'],
	':sid' => $sid,
	':stat_day' => date('Ymd'),
);
$stat['total_order'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition, $params));
$stat['total_fee'] = floatval(pdo_fetchcolumn('select round(sum(total_fee), 2) from ' . tablename('tiny_wmall_order') . $condition, $params));
$stat['final_fee'] = floatval(pdo_fetchcolumn('select round(sum(store_final_fee), 2) from ' . tablename('tiny_wmall_order') . $condition, $params));

$notice = store_notice_stat($_W['manager']['id']);

$ads = pdo_fetchall('select * from ' . tablename('tiny_wmall_slide') . ' where uniacid = :uniacid and type = 3 and status = 1 order by displayorder desc', array(':uniacid' =>  $_W['uniacid']));

include itemplate('shop/index');