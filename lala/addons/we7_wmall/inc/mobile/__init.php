<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('common');
mload()->func('app');
mload()->func('tpl.app');
mload()->model('member');
mload()->model('store');
mload()->model('order');
$_W['is_agent'] = is_agent();
$_W['agentid'] = 0;
if($_W['is_agent']) {
	mload()->model('agent');
	if(in_array($_GPC['ctrl'], array('wmall')) && in_array($_GPC['ac'], array('home', 'channel'))) {
		$_W['agentid'] = intval($_GPC['__agentid']);
		if($_W['agentid'] <= 0) {
			$location = array($_GPC['__lat'], $_GPC['__lng']);
			$_W['agentid'] = get_location_agent($location[0], $location[1]);
		}
		if(!$_W['isajax'] && $_GPC['ac'] == 'home' && !empty($_GPC['lat'])) {
			$location = array($_GPC['lat'], $_GPC['lng']);
			$_W['agentid'] = get_location_agent($location[0], $location[1]);
		}
		$_W['agent'] = get_agent($_W['agentid'], array('id', 'area'));
		if(empty($_W['agent']) && $_GPC['op'] != 'agent') {
			if($_GPC['d']) {
				$_W['mobile_tpl'] = 'default';
				$msg = array(
					'title' => '附近没有发现门店,我们正在努力覆盖中',
					'btn_text' => '切换地址',
				);
				imessage($msg, imurl('wmall/home/agent'), 'info');
			} else {
				header('location:' . imurl('wmall/home/agent'));
				die;
			}
		}
	}
}
$_W['we7_wmall']['config'] = get_system_config();
$_config_mall = $_W['we7_wmall']['config']['mall'];
if(empty($_config_mall['delivery_title'])) {
	$_config_mall['delivery_title'] = '平台专送';
}
$mobile_template = $_config_mall['template_mobile'];
if(empty($mobile_template) || $mobile_template == 'template') {
	$mobile_template = 'default';
}
$_W['mobile_tpl'] = $mobile_template;
define('WE7_WMALL_TPL_ROOT', WE7_WMALL_LOCAL . "/template/mobile/wmall/{$mobile_template}/");
define('WE7_WMALL_TPL_URL', WE7_WMALL_URL . "/template/mobile/wmall/{$mobile_template}/");

$config_close = $_W['we7_wmall']['config']['close'];
if($config_close['status'] == 2 && $_W['_controller'] == 'wmall') {
	if(!empty($config_close['url'])) {
		header('location:' . $config_close['url']);
		die;
	}
	$tips = !empty($config_close['tips']) ? $config_close['tips'] : '亲,平台休息中。。。';
	imessage($tips, 'close', 'info');
}

$_W['role'] = 'consumer';
$_W['role_cn'] = '下单顾客';


