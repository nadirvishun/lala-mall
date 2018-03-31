<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$_W['page']['title'] = '全部资讯';
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_news_category') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	$condition = ' where uniacid = :uniacid and is_display = 1';
	$params[':uniacid'] = $_W['uniacid'];
	$cateid = intval($_GPC['cateid']);

	if (!empty($cateid)) {
		$condition .= ' and cateid = :cateid';
		$params['cateid'] = $cateid;
	}

	$news = pdo_fetchall('select * from ' . tablename('tiny_wmall_news') . $condition . ' order by displayorder desc', $params);

	foreach ($news as &$row) {
		$row['desc'] = cutstr($row['desc'], 10, true);
	}
}

if ($ta == 'detail') {
	$_W['page']['title'] = '资讯详情';
	$id = intval($_GPC['id']);

	if (!empty($id)) {
		$news = pdo_fetch('select * from ' . tablename('tiny_wmall_news') . ' where uniacid = :uniacid and id = :id and is_display = 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	}
}

include itemplate('store/shop/news');

?>
