<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');
$_W['page']['title'] = '商家详情';
$id = $sid = intval($_GPC['sid']);
$store = store_fetch($id);

if (0 < $_W['member']['uid']) {
	$is_favorite = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
}

$activity = store_fetch_activity($sid);
$stat = store_comment_stat($sid);
$stat['all'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => 1));

if (!empty($comment)) {
	$comment['data'] = iunserializer($comment['data']);
	$comment['score'] = ($comment['delivery_service'] + $comment['goods_quality']) * 10;
	$comment['mobile'] = str_replace(substr($comment['mobile'], 4, 4), '****', $comment['mobile']);
	$comment['addtime'] = date('Y-m-d H:i', $comment['addtime']);
	$comment['avatar'] = tomedia($comment['avatar']) ? tomedia($comment['avatar']) : WE7_WMALL_TPL_URL . 'static/img/head.png';
	$comment['thumbs'] = iunserializer($comment['thumbs']);

	if (!empty($comment['thumbs'])) {
		foreach ($comment['thumbs'] as &$item) {
			$item = tomedia($item);
		}
	}
}

include itemplate('home/newStore');

?>
