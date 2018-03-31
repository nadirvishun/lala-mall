<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->func('tpl.app');
icheckauth();
$_W['page']['title'] = '订单评价';

$id = intval($_GPC['id']);
$order = order_fetch($id);
if(!$_W['ispost']) {
	if(empty($order)) {
		imessage('订单不存在或已删除', '', 'error');
	}
	$goods = order_fetch_goods($order['id']);
} else {
	if(empty($order)) {
		imessage(error(-1, '订单不存在或已删除'), '', 'ajax');
	}
	if($order['is_comment'] == 1) {
		imessage(error(-1, '订单已评价'), '', 'ajax');
	}

	$store = store_fetch($order['sid'], array('comment_status'));
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['member']['uid'],
		'username' => $order['username'],
		'avatar' => $_W['member']['avatar'],
		'mobile' => $order['mobile'],
		'oid' => $id,
		'sid' => $order['sid'],
		'goods_quality' => intval($_GPC['goods_quality']) ? intval($_GPC['goods_quality']) : 5,
		'delivery_service' => intval($_GPC['delivery_service']) ? intval($_GPC['delivery_service']) : 5,
		'note' => trim($_GPC['note']),
		'status' => $store['comment_status'],
		'data' => '',
		'addtime' => TIMESTAMP,
	);
	if(!empty($_GPC['thumbs'])) {
		$thumbs = array();
		foreach($_GPC['thumbs'] as $thumb) {
			if(empty($thumb)) continue;
			$thumbs[] = $thumb;
		}
		$insert['thumbs'] = iserializer($thumbs);
	}
	$goods = order_fetch_goods($order['id']);
	foreach($goods as $good) {
		$value = intval($_GPC['goods'][$good['id']]);
		if(!$value) {
			continue;
		}
		$update = ' set comment_total = comment_total + 1';
		if($value == 1) {
			$update .= ' , comment_good = comment_good + 1';
			$insert['data']['good'][] = $good['goods_title'];
		} else {
			$insert['data']['bad'][] = $good['goods_title'];
		}
		pdo_query('update ' . tablename('tiny_wmall_goods') . $update . ' where id = :id', array(':id' => $good['goods_id']));
	}
	$insert['score'] = $insert['goods_quality'] + $insert['delivery_service'];
	$insert['data'] = iserializer($insert['data']);
	pdo_insert('tiny_wmall_order_comment', $insert);
	pdo_update('tiny_wmall_order', array('is_comment' => 1), array('id' => $id));
	if($store['comment_status'] == 1) {
		store_comment_stat($order['sid']);
	}
	//推广员佣金入账
	if(check_plugin_perm('spread')) {
		mload()->model('plugin');
		pload()->model('spread');
		member_spread_confirm();
		spread_order_balance($order['id']);
	}
	imessage(error(0, ''), '', 'ajax');
}
include itemplate('order/comment');

