<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '订单分享设置';

if ($_W['ispost']) {
	if (intval($_GPC['share_grant_days_limit']) <= 0) {
		imessage(error(-1, '下单后有效分享天数必须大于0'), 'refresh', 'ajax');
	}

	$share = array('title' => trim($_GPC['title']), 'desc' => trim($_GPC['desc']), 'imgUrl' => trim($_GPC['imgUrl']));
	$data = array('status' => intval($_GPC['status']), 'grantType' => trim($_GPC['grantType']), 'grantType_cn' => '积分', 'share_grant' => floatval($_GPC['share_grant']), 'share_grant_max' => floatval($_GPC['share_grant_max']), 'share_grant_days_limit' => intval($_GPC['share_grant_days_limit']), 'share' => $share);

	if ($data['grantType'] == 'credit2') {
		$data['grantType_cn'] = '元';
	}

	set_plugin_config('ordergrant.share', $data);
	imessage(error(0, '分享订单设置成功'), 'refresh', 'ajax');
}

$config_share = get_plugin_config('ordergrant.share');
include itemplate('share');

?>
