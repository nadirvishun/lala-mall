<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');

if ($op == 'index') {
	$_W['page']['title'] = '天天特价设置';

	if ($_W['ispost']) {
		$share = array('title' => trim($_GPC['title']), 'imgUrl' => trim($_GPC['imgUrl']), 'desc' => trim($_GPC['desc']), 'link' => trim($_GPC['link']));
		$bargain = array('status' => intval($_GPC['status']), 'is_home_display' => intval($_GPC['is_home_display']), 'template' => intval($_GPC['template']), 'thumb' => trim($_GPC['thumb']), 'agreement' => htmlspecialchars_decode($_GPC['agreement']), 'share' => $share);
		set_plugin_config('bargain', $bargain);
		imessage(error(0, '设置天天特价活动成功'), 'refresh', 'ajax');
	}

	$config_bargain = get_plugin_config('bargain');
}

include itemplate('index');

?>
