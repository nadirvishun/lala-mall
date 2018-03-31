<?php
//微擎应用 http://www.we7.cc   
function finance_amount_stat($sid = 0)
{
	global $_W;
	$stat = array();
	$today_starttime = strtotime(date('Y-m-d'));
	$month_starttime = strtotime(date('Y-m'));
	$condition = ' where uniacid = :uniacid and status = 5 and is_pay = 1 and addtime >= :starttime';
	$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $today_starttime);

	if (0 < $sid) {
		$condition .= ' and sid = :sid';
		$params[':sid'] = $sid;
	}

	$stat['today_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition, $params));
	$stat['today_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . $condition, $params));
	$params[':starttime'] = $month_starttime;
	$stat['month_total'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . $condition, $params));
	$stat['month_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . $condition, $params));
	return $stat;
}

defined('IN_IA') || exit('Access Denied');

?>
