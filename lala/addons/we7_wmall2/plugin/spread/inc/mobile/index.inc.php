<?php
/**
 * 外送系统
 * @author 微猫源码
 * @QQ 2058430070
 * @url http://www.weixin2015.cn/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$_W['page']['title'] = '推广员';

$member = get_spread();
if(empty($member['is_spread']) || $member['spread_status'] != 1) {
	header('Location:' . imurl('spread/register'));
	die;
}

$rank = get_plugin_config('rank');

$basic = get_plugin_config('spread.basic');
$down1 = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members') . 'where uniacid = :uniacid and spread1 = :spread', array(':uniacid' => $_W['uniacid'], ':spread' => $_W['member']['uid']));
$down2 = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_members') . 'where uniacid = :uniacid and spread2 = :spread', array(':uniacid' => $_W['uniacid'], ':spread' => $_W['member']['uid']));

$condition = " where uniacid = :uniacid and is_pay = 1 ";
$params = array(
	':uniacid' => $_W['uniacid'],
	':spread' => $_W['member']['uid'],
);
if($basic['level'] == 2) {
	$down = $down1 + $down2;
	$condition .= " and (spread1 = :spread or spread2 = :spread)";
} elseif($basic['level'] == 1) {
	$down = $down1;
	$condition .= " and spread1 = :spread";
}
$order = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_order') . $condition, $params);
$commission = pdo_fetchcolumn('select count(*) from'. tablename('tiny_wmall_spread_getcash_log') .' where uniacid = :uniacid and spreadid = :spreadid', array(':uniacid' => $_W['uniacid'], ':spreadid' => $_W['member']['uid']));
$current = pdo_fetchcolumn('select count(*) from' .tablename('tiny_wmall_spread_current_log') . 'where uniacid = :uniacid and spreadid = :spreadid', array(':uniacid' => $_W['uniacid'], ':spreadid' => $_W['member']['uid']));
$spread = spread_commission_stat();
$upgrade_explain = get_config_text("spread:upgrade_explain");

spread_group_update($_W['member']['uid']);

include itemplate('index');


 