<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'favorite') {
	$id = intval($_GPC['id']);
	$type = trim($_GPC['type']);

	if ($type == 'star') {
		$store = store_fetch($id, array('id', 'title'));

		if (empty($store)) {
			imessage(error(-1, '门店不存在'), '', 'ajax');
		}

		$data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id, 'addtime' => TIMESTAMP);
		$is_exist = pdo_get('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));

		if (empty($is_exist)) {
			pdo_insert('tiny_wmall_store_favorite', $data);
		}
	}
	else {
		pdo_delete('tiny_wmall_store_favorite', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'sid' => $id));
	}

	imessage(error(0, ''), '', 'ajax');
}

?>
