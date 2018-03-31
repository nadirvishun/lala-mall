<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$spread = error(-1, '');
if(check_plugin_perm('spread')) {
	mload()->model('plugin');
	pload()->model('spread');
	$spread = member_spread_bind();
}

$_W['page']['title'] = "会员中心";
$user = $_W['member'];
$user['nickname'] = $_W['member']['nickname'];
$user['avatar'] = $_W['member']['avatar'];

$config_settle = $_W['we7_wmall']['config']['store']['settle'];
$config_mall = $_W['we7_wmall']['config']['mall'];
$config_delivery = $_W['we7_wmall']['config']['delivery'];
$favorite = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_store_favorite') . ' where uniacid = :uniacid and uid = :uid', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'])));
$coupon_nums = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_coupon_record') . ' where uniacid = :uniacid and uid = :uid and status = 1', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'])));
$redpacket_nums = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and uid = :uid and status = 1', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'])));

$deliveryCard_status = check_plugin_perm('deliveryCard') && get_plugin_config('deliveryCard.card_apply_status');
$redpacket_status = check_plugin_perm('shareRedpacket') || check_plugin_perm('freeLunch') || check_plugin_perm('superRedpacket');

$urls = array(
	'coupon' => array(
		'title' => '我的代金券',
		'url' => imurl('wmall/member/coupon'),
		'css' => '',
		'icon' => 'mypage_token.png',
	),
	'address' => array(
		'title' => '我的地址',
		'url' => imurl('wmall/member/address'),
		'css' => '',
		'icon' => 'mypage_location.png',
	),
	'favorite' => array(
		'title' => '我的收藏',
		'url' => imurl('wmall/member/favorite'),
		'css' => '',
		'icon' => 'mypage_star.png',
	),
	'comment' => array(
		'title' => '我的评价',
		'url' => imurl('wmall/member/comment'),
		'css' => '',
		'icon' => 'mypage_comment.png',
	),
	'recharge' => array(
		'title' => '余额充值',
		'url' => imurl('wmall/member/recharge'),
		'css' => '',
		'icon' => 'mypage_credit.png',
	),
	'help' => array(
		'title' => '常见问题',
		'url' => imurl('wmall/home/help'),
		'css' => '',
		'icon' => 'mypage_problem.png',
	),

);
if(!empty($redpacket_status)) {
	$urls['redpacket'] = array(
		'title' => '我的红包',
		'url' => imurl('wmall/member/redPacket'),
		'css' => '',
		'icon' => 'mypage_token.png',
	);
}
if(!empty($deliveryCard_status)) {
	$urls['deliveryCard'] = array(
		'title' => '配送会员卡',
		'url' => imurl('deliveryCard/index'),
		'css' => '',
		'icon' => 'mypage_vip.png',
	);
}
if(check_plugin_perm('shareRedpacket')) {
	$urls['shareRedpacket'] = array(
		'title' => '分享有礼',
		'url' => imurl('shareRedpacket/index'),
		'css' => '',
		'icon' => 'mypage_share.png',
	);
}
if(check_plugin_perm('ordergrant') && get_plugin_config('ordergrant.status')) {
	$urls['ordergrant'] = array(
		'title' => '下单有礼',
		'url' => imurl('ordergrant/index'),
		'css' => 'active',
		'icon' => 'mypage_share.png',
	);
}
if(check_plugin_perm('spread')) {
	$config_spread = get_plugin_config('spread.basic');
	if($config_spread['show_in_mine']) {
		$urls['spread'] = array(
			'title' => $config_spread['menu_name'],
			'url' => imurl('spread/index'),
			'css' => 'active',
			'icon' => 'mypage_share.png',
		);
	}
}
$urls = array_chunk($urls, 4);

include itemplate('member/mine');


