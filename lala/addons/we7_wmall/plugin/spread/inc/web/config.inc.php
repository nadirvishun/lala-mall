<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'basic') {
	$_W['page']['title'] = '基础设置';
	if($_W['ispost']) {
		$dataBasic = array(
			'level' => intval($_GPC['level']),
			'show_in_mine' => intval($_GPC['show_in_mine']),
			'menu_name' => trim($_GPC['menu_name']),
		);
		$dataRelate = array(
			'become_child' => intval($_GPC['become_child']),
			'become' => intval($_GPC['become']),
			'open_protocol' => intval($_GPC['open_protocol']),
			'become_ordercount' => intval($_GPC['become_ordercount']),
			'become_moneycount' => intval($_GPC['become_moneycount']),
			'become_check' => intval($_GPC['become_check']),
			'become_reg' => intval($_GPC['become_reg']),
			'group_update_mode' => trim($_GPC['group_update_mode']),
		);
		$dataSettle = array(
			'withdraw' => floatval($_GPC['withdraw']),
			'withdrawcharge' => floatval($_GPC['withdrawcharge']),
			'balance_condition' => intval($_GPC['balance_condition']),
		);
		$cashcredit = array_filter($_GPC['cashcredit'], trim);
		if(empty($cashcredit)) {
			imessage(error(-1, '至少选择一种提现方式'), '', 'ajax');
		}
		$dataSettle['cashcredit'] = $cashcredit;
		$dataTemplate = array(
			'avatar' => trim($_GPC['avatar']),
			'spread' => trim($_GPC['spread']),
			'shop' => trim($_GPC['shop']),
			'myshop' => trim($_GPC['myshop']),
		);
		$dataProtocol = $_GPC['protocol'];
		$upgrade_explain = $_GPC['upgrade_explain'];
		set_plugin_config('spread.basic', $dataBasic);
		set_plugin_config('spread.relate', $dataRelate);
		set_plugin_config('spread.settle', $dataSettle);
		set_plugin_config('spread.template', $dataTemplate);
		set_config_text("spread:agreement", htmlspecialchars_decode($dataProtocol));
		set_config_text("spread:upgrade_explain", htmlspecialchars_decode($upgrade_explain));
		imessage(error(0, '推广员基础设置成功'), 'refresh', 'ajax');
	}
	$basic = get_plugin_config('spread.basic');
	$relate = get_plugin_config('spread.relate');
	$settle = get_plugin_config('spread.settle');
	$template = get_plugin_config('spread.template');
	$protocol = get_config_text("spread:agreement");
	$upgrade_explain = get_config_text("spread:upgrade_explain");
}
include itemplate('config');