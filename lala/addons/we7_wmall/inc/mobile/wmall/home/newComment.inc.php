<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '评价列表';
icheckauth();
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);

if (empty($store)) {
	imessage('门店不存在或已经删除', referer(), 'error');
}

$activity = store_fetch_activity($sid);
mload()->model('activity');
activity_store_cron($sid);
$stat = store_comment_stat($sid);
$stat['all'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['good'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 8', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['middle'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 4 and score <= 7', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['bad'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score <= 3', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.status = 1';
$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
$type = intval($_GPC['type']);

if ($type == 1) {
	$condition .= ' and a.score >= 8';
}
else if ($type == 2) {
	$condition .= ' and a.score >= 4 and a.score <= 7';
}
else {
	if ($type == 3) {
		$condition .= ' and a.score <= 3';
	}
}

$note = intval($_GPC['note']);

if (0 < $note) {
	$condition .= ' and a.note != \'\'';
}

$id = intval($_GPC['min']);

if (0 < $id) {
	$condition .= ' and a.id < :id';
	$params[':id'] = $id;
}

$comments = pdo_fetchall('select a.id as aid, a.*, b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id ' . $condition . ' order by a.id desc limit 5', $params, 'aid');
$min = 0;

if (!empty($comments)) {
	foreach ($comments as &$row) {
		$row['data'] = iunserializer($row['data']);
		$row['score'] = ($row['delivery_service'] + $row['goods_quality']) * 10;
		$row['mobile'] = str_replace(substr($row['mobile'], 4, 4), '****', $row['mobile']);
		$row['addtime'] = date('Y-m-d H:i', $row['addtime']);
		$row['replytime'] = date('Y-m-d H:i', $row['replytime']);
		$row['avatar'] = tomedia($row['avatar']) ? tomedia($row['avatar']) : WE7_WMALL_TPL_URL . 'static/img/head.png';
		$row['thumbs'] = iunserializer($row['thumbs']);

		if (!empty($row['thumbs'])) {
			foreach ($row['thumbs'] as &$item) {
				$item = tomedia($item);
			}
		}
	}

	$min = min(array_keys($comments));
}

if ($_W['ispost']) {
	$comments = array_values($comments);
	$respon = array('errno' => 0, 'message' => $comments, 'min' => $min);
	imessage($respon, '', 'ajax');
}

include itemplate('home/newComment');

?>
