<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '奖励记录';
$condition = ' where a.uniacid = :uniacid';
$params = array(':uniacid' => $_W['uniacid']);
$type = (isset($_GPC['type']) ? intval($_GPC['type']) : -1);

if (-1 < $type) {
	$condition .= ' and a.type = :type';
	$params[':type'] = $type;
}

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
$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order_grant_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition, $params);
$records = pdo_fetchall('select a.*,b.realname,b.avatar from ' . tablename('tiny_wmall_order_grant_record') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid' . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
$pager = pagination($total, $pindex, $psize);
$labels = grant_types();
include itemplate('record');

?>
