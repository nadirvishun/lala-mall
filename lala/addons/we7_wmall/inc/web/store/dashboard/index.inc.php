<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '运营概况';
	$news = pdo_fetchall('select * from ' . tablename('tiny_wmall_news') . ' where uniacid = :uniacid and is_display = 1 and is_show_home = 1 order by displayorder desc limit 5', array(':uniacid' => $_W['uniacid']));

	if (!empty($news)) {
		foreach ($news as &$row) {
			$row['desc'] = cutstr($row['desc'], 20, true);
		}

		$news_one = $news[0];
		unset($news[0]);
	}

	$ads = pdo_fetchall('select * from ' . tablename('tiny_wmall_slide') . ' where uniacid = :uniacid and type = 3 and status = 1 order by displayorder desc', array(':uniacid' => $_W['uniacid']));
}

include itemplate('store/dashboard/index');

?>
