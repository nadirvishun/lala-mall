<?php
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = ((trim($_GPC['op']) ? trim($_GPC['op']) : 'settle'));
if ($op == 'settle') 
{
	$_W['page']['title'] = '配送员申请';
	if ($_W['ispost']) 
	{
		$settle = array('mobile_verify_status' => intval($_GPC['mobile_verify_status']), 'idCard' => intval($_GPC['idCard']));
		set_config_text('agreement_delivery', htmlspecialchars_decode($_GPC['agreement_delivery']));
		set_system_config('delivery.settle', $settle);
		imessage(error(0, '配送员申请设置成功'), referer(), 'ajax');
	}
	$settle = $_config['delivery']['settle'];
	$settle['agreement_delivery'] = get_config_text('agreement_delivery');
	include itemplate('config/deliveryer-settle');
}
if ($op == 'cash') 
{
	$_W['page']['title'] = '配送员提成';
	if ($_W['ispost']) 
	{
		$cash = array('get_cash_fee_limit' => intval($_GPC['get_cash_fee_limit']), 'get_cash_fee_rate' => trim($_GPC['get_cash_fee_rate']), 'get_cash_fee_min' => intval($_GPC['get_cash_fee_min']), 'get_cash_fee_max' => intval($_GPC['get_cash_fee_max']));
		set_system_config('delivery.cash', $cash);
		imessage(error(0, '配送员提现设置成功'), referer(), 'ajax');
	}
	$cash = $_config['delivery']['cash'];
	include itemplate('config/deliveryer-cash');
}
?>