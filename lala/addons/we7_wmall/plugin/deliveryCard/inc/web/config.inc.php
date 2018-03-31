<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');

if ($op == 'index') {
	$_W['page']['title'] = '基础设置';

	if ($_W['ispost']) {
		$deliveryCard = array('card_apply_status' => intval($_GPC['card_apply_status']));
		set_plugin_config('deliveryCard', $deliveryCard);
		set_config_text('agreement_card', htmlspecialchars_decode($_GPC['agreement_card']));
		imessage(error(0, '设置配送会员卡参数成功'), 'refresh', 'ajax');
	}

	$agreement_card = get_config_text('agreement_card');
}

include itemplate('config');

?>
