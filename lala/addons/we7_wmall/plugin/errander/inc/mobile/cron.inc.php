<?php
defined('IN_IA') || exit('Access Denied');
$config_errander = $_config_plugin;
p($config_errander);
if (0 < $config_errander['pay_time_limit']) 
{
	$orders = pdo_fetchall('select id, addtime from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and is_pay = 0 and status = 1 and addtime <= :addtime limit 5', array(':uniacid' => $_W['uniacid'], ':addtime' => time() - ($config_errander['pay_time_limit'] * 60)));
	if (!(empty($orders))) 
	{
		$extra = array('note' => '提交订单' . $config_errander['pay_time_limit'] . '分钟内未支付, 系统已自动取消订单');
		foreach ($orders as $order ) 
		{
			errander_order_status_update($order['id'], 'cancel', $extra);
		}
	}
}
if (0 < $config_errander['handle_time_limit']) 
{
	$orders = pdo_fetchall('select id, addtime from ' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and is_pay = 1 and status = 1 and paytime <= :paytime limit 5', array(':uniacid' => $_W['uniacid'], ':paytime' => time() - ($config_errander['handle_time_limit'] * 60)));
	if (!(empty($orders))) 
	{
		$extra = array('note' => '平台' . $config_errander['handle_time_limit'] . '分钟内未接单, 系统已自动取消订单');
		foreach ($orders as $order ) 
		{
			errander_order_status_update($order['id'], 'cancel', $extra);
		}
	}
}
if (0 < $config_errander['auto_success_hours']) 
{
	$orders = pdo_fetchall('select id, delivery_status, delivery_assign_time from' . tablename('tiny_wmall_errander_order') . ' where uniacid = :uniacid and status < 3 and delivery_status > 1 and delivery_status < 4 and delivery_assign_time < :delivery_assign_time ', array(':uniacid' => $_W['uniacid'], ':delivery_assign_time' => time() - ($config_errander['auto_success_hours'] * 3600)));
	if (!(empty($orders))) 
	{
		$extra = array('note' => '系统已自动完成订单');
		foreach ($orders as $order ) 
		{
			errander_order_status_update($order['id'], 'end', $extra);
		}
	}
}
?>