<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';
if($ta == 'list') {
	$_W['page']['title'] = '用户评价';

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' WHERE a.uniacid = :uniacid AND a.sid = :sid';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':sid' => $sid,
	);

	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status >= 0) {
		$condition .= " AND a.status = :status";
		$params[':status'] = $status;
	}

	$reply = isset($_GPC['reply']) ? intval($_GPC['reply']) : -1;
	if($reply == 0) {
		$condition .= " AND a.reply = ''";
	} elseif($reply == 1) {
		$condition .= " AND a.reply != ''";
	}

	$note = isset($_GPC['note']) ? intval($_GPC['note']) : -1;
	if($note == 1) {
		$condition .= " AND a.note != ''";
	}

	$goods_quality = isset($_GPC['goods_quality']) ? intval($_GPC['goods_quality']) : -1;
	if($goods_quality > 0) {
		$condition .= " AND a.goods_quality = {$_GPC['goods_quality']}";
	}
	$delivery_service = isset($_GPC['delivery_service']) ? intval($_GPC['delivery_service']) : -1;
	if($delivery_service > 0) {
		$condition .= " AND a.delivery_service = {$_GPC['delivery_service']}";
	}

	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-15 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " AND a.addtime > :start AND a.addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_order_comment') . ' AS a '.  $condition, $params);
	$comments = pdo_fetchall('SELECT a.*, b.uid,b.openid,b.addtime FROM ' . tablename('tiny_wmall_order_comment') . ' AS a LEFT JOIN ' . tablename('tiny_wmall_order') . ' AS b ON a.oid = b.id ' . $condition . ' ORDER BY a.addtime DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	if(!empty($comments)) {
		foreach ($comments as &$row) {
			$row['score'] = ($row['delivery_service'] + $row['goods_quality']) / 2;
			$row['data'] = iunserializer($row['data']);
			$row['mobile'] = str_replace(substr($row['mobile'], 4, 4), '****', $row['mobile']);
			$row['thumbs'] = iunserializer($row['thumbs']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

if($ta == 'status') {
	if(empty($store['self_audit_comment'])) {
		imessage(error(-1, '店铺不能自己审核评论'), '', 'ajax');
	}
	$id = intval($_GPC['id']);
	$comment = pdo_get('tiny_wmall_order_comment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($comment)) {
		imessage(error(-1, '评论不存在或已删除'), '', 'ajax');
	}
	pdo_update('tiny_wmall_order_comment', array('status' => intval($_GPC['status'])), array('uniacid' => $_W['uniacid'], 'id' => $id));
	store_comment_stat($comment['sid']);
	imessage(error(0, '设置评论状态成功'), referer(), 'ajax');
}

if($ta == 'reply') {
	if(!$_W['isajax']) {
		return false;
	}
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已经删除'), '', 'ajax');
	}
	$reply = trim($_GPC['reply']);
	$update = array(
		'reply' => $reply,
		'replytime' => TIMESTAMP,
	);
	if($store['self_audit_comment'] == 1) {
		$update['status'] = 1;
	}
	pdo_update('tiny_wmall_order_comment', $update, array('uniacid' => $_W['uniacid'], 'oid' => $id));
	store_comment_stat($order['sid']);
	imessage(error(0, ''), '', 'ajax');
}
include itemplate('store/service/comment');




