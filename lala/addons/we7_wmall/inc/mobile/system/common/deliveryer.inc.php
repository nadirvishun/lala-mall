<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'location');

if ($ta == 'location') {
	$id = intval($_GPC['id']);
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));

	if (empty($deliveryer)) {
		imessage(error(-1, '配送员不存在或已删除'), '', 'ajax');
	}

	$deliveryer['avatar'] = tomedia($deliveryer['avatar']);
	imessage(error(0, $deliveryer), '', 'ajax');
}

?>
