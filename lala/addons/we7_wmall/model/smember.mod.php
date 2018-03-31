<?php
//微擎应用 http://www.we7.cc   
function smember_amount_stat()
{
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$yesterday_starttime = $today_starttime - 86400;
	$month_starttime = strtotime(date('Y-m'));
	$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and first_order_time >= :starttime and first_order_time <= :endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
	$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and first_order_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $today_starttime)));
	$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and first_order_time >= :starttime', array(':uniacid' => $_W['uniacid'], ':starttime' => $month_starttime)));
	$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid'])));
	return $stat;
}

defined('IN_IA') || exit('Access Denied');

?>
