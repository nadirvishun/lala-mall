<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '积分商城兑换记录';
$condition = ' where a.uniacid = :uniacid and a.status = 1';
$params = array(':uniacid' => $_W['uniacid']);

if (!empty($_GPC['addtime'])) {
	$starttime = strtotime($_GPC['addtime']['start']);
	$endtime = strtotime($_GPC['addtime']['end']) + 86399;
}
else {
	$today = strtotime(date('Y-m-d'));
	$starttime = strtotime('-15 day', $today);
	$endtime = $today + 86399;
}

$condition .= ' and a.addtime >= :starttime and a.addtime <= :endtime';
$params[':starttime'] = $starttime;
$params[':endtime'] = $endtime;
$keyword = trim($_GPC['keyword']);

if (!empty($keyword)) {
	$condition .= ' and (b.realname like \'%' . $keyword . '%\' or b.nickname like \'%' . $keyword . '%\' or mobile like \'%' . $keyword . '%\')';
}

$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_creditshop_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
$orders = pdo_fetchall('select a.*,b.avatar,b.nickname from ' . tablename('tiny_wmall_creditshop_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' order by a.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
$pager = pagination($total, $pindex, $psize);
include itemplate('order');

?>
