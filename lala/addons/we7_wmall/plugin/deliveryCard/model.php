<?php
defined('IN_IA') || exit('Access Denied');
function card_setmeal_buy_notice($order_id, $note = '') 
{
	global $_W;
	$order = pdo_get('tiny_wmall_delivery_cards_order', array('uniacid' => $_W['uniacid'], 'id' => $order_id));
	if (empty($order)) 
	{
		return error(-1, '订单不存在');
	}
	$pay_types = order_pay_types();
	$order['pay_type_cn'] = $pay_types[$order['pay_type']]['text'];
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $order['uid']));
	$card = pdo_get('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $order['card_id']));
	if (empty($card)) 
	{
		return error(-1, '套餐不存在或已删除');
	}
	$acc = WeAccount::create($order['acid']);
	$maneger = $_W['we7_wmall']['config']['manager'];
	if (!(empty($maneger['openid']))) 
	{
		$tips = '配送会员卡【' . $card['title'] . '】售出通知';
		$remark = array('购卡费用: ' . $order['final_fee'] . '元', '支付方式: ' . $order['pay_type_cn'], '购买　人: ' . (($member['realname'] ? $member['realname'] : $member['nickname'])), '联系方式: ' . $member['mobile'], '购买套餐: ' . $card['title'], '套餐期限: ' . date('Y-m-d', $order['starttime']) . '~' . date('Y-m-d', $order['endtime']), '购买时间: ' . date('Y-m-d H:i:s', $order['paytime']));
		$params = array('first' => $tips, 'OrderSn' => $order['ordersn'], 'OrderStatus' => '已生效', 'remark' => implode("\n", $remark));
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($maneger['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send);
		if (is_error($status)) 
		{
			slog('wxtplNotice', '配送会员卡售出微信通知平台管理员', $send, $status['message']);
		}
	}
	if (!(empty($order['openid']))) 
	{
		$tips = '您成功订购了配送会员卡【' . $card['title'] . '】';
		$remark = array('购卡费用: ' . $order['final_fee'] . '元', '支付方式: ' . $order['pay_type_cn'], '购买套餐: ' . $card['title'], '套餐期限: ' . date('Y-m-d', $order['starttime']) . '~' . date('Y-m-d', $order['endtime']), '购买时间: ' . date('Y-m-d H:i:s', $order['paytime']));
		$params = array('first' => $tips, 'OrderSn' => $order['ordersn'], 'OrderStatus' => '已生效', 'remark' => implode("\n", $remark));
		$url = imurl('deliveryCard/index', array(), true);
		$send = sys_wechat_tpl_format($params);
		$status = $acc->sendTplNotice($order['openid'], $_W['we7_wmall']['config']['notice']['wechat']['public_tpl'], $send, $url);
		if (is_error($status)) 
		{
			slog('wxtplNotice', '配送会员卡售出微信通知购买人', $send, $status['message']);
		}
	}
	return true;
}
function card_setmeal_buy($order_id) 
{
	global $_W;
	$order = pdo_get('tiny_wmall_delivery_cards_order', array('id' => $order_id, 'uniacid' => $_W['uniacid']));
	if (empty($order)) 
	{
		return error(-1, '订单不存在');
	}
	if (!($order['is_pay'])) 
	{
		$card = pdo_get('tiny_wmall_delivery_cards', array('id' => $order['card_id'], 'uniacid' => $_W['uniacid']));
		if (!(empty($card))) 
		{
			$member = pdo_get('tiny_wmall_members', array('uid' => $order['uid'], 'uniacid' => $_W['uniacid']));
			if (!(empty($member))) 
			{
				$starttime = strtotime(date('Y-m-d'));
				if ($starttime < $member['setmeal_endtime']) 
				{
					$starttime = $member['setmeal_endtime'];
				}
				$endtime = strtotime($card['days'] . 'days', $starttime);
				pdo_update('tiny_wmall_delivery_cards_order', array('is_pay' => 1, 'paytime' => TIMESTAMP, 'starttime' => $starttime, 'endtime' => $endtime), array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
				pdo_update('tiny_wmall_members', array('setmeal_id' => $card['id'], 'setmeal_day_free_limit' => $card['day_free_limit'], 'setmeal_starttime' => $starttime, 'setmeal_endtime' => $endtime), array('uid' => $member['uid'], 'uniacid' => $_W['uniacid']));
				card_setmeal_buy_notice($order['id']);
			}
		}
	}
	return true;
}
?>