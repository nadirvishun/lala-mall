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
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$_W['page']['title'] = '推广海报';
$settle = get_plugin_config('spread.settle');
$relate = get_plugin_config('spread.relate');
if($op == 'index') {
	mload()->model('poster');
	mload()->model('qrcode');
	$_config_plugin['poster']['data'] = json_decode(base64_decode($_config_plugin['poster']['data']), true);
	$_config_qrcode = $_config_plugin['poster']['qrcode'];
	if($_config_qrcode['params']['type'] == 'system' || empty($_config_qrcode['params']['type'])) {
		$url = imurl('wmall/member/mine', array('code' => $_W['member']['uid']), true);
		$params = array(
			'url' => $url,
			'size' => 4,
		);
		$qrcode_url = qrcode_normal_build($params);
		if(is_error($qrcode)) {
			$respon = array('errno' => 1, 'message' => '生成二维码失败');
			imessage($respon, '', 'ajax');
		}
	} else {
		$params = array(
			'scene_str' => "we7_wmall_spread_{$_W['uniacid']}_{$_W['member']['uid']}",
			'qrcode_type' => 'fixed',
			'uid' => $_W['member']['uid'],
			'name' => "外卖推广海报",
		);
		$qrcode = qrcode_wechat_build($params);

		if(is_error($qrcode)) {
			$respon = array('errno' => 1, 'message' => "生成二维码失败{$qrcode['message']}");
			imessage($respon, '', 'ajax');
		}
		$qrcode_url = qrcode_url($qrcode['ticket']);
	}
	$_config_plugin['poster']['qrcode_url'] = $qrcode_url;
	$params = array(
		'config' => $_config_plugin['poster'],
		'member' => $_W['member'],
		'name' => "spread_{$_W['member']['uid']}",
		'plugin' => 'spread',
	);
	$url = poster_create($params);
	if(is_error($data)) {
		$respon = array('errno' => 1, 'message' => "生成海报失败");
		imessage($respon, '', 'ajax');
	}
	$respon = array('errno' => 0, 'message' => $url);
	imessage($respon, '', 'ajax');
}
include itemplate('poster');

