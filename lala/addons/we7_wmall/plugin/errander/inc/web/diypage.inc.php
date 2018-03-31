<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'post');

if ($op == 'post') {
	$_W['page']['title'] = '跑腿页面编辑';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$_GPC['data']['params'] = array('type' => 'buy', 'container' => '.head-banner');
		$insert = array('uniacid' => $_W['uniacid'], 'data' => base64_encode(json_encode($_GPC['data'])), 'updatetime' => TIMESTAMP);

		if (!empty($id)) {
			pdo_update('tiny_wmall_errander_diypage', $insert, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		else {
			$insert['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_errander_diypage', $insert);
			$id = pdo_insertid();
		}

		imessage(error(0, '添加成功'), iurl('errander/diypage/post', array('id' => $id)), 'ajax');
	}

	if (!empty($id)) {
		$diypage = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_errander_diypage') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (!empty($diypage)) {
			$diypage['data'] = json_decode(base64_decode($diypage['data']), true);
		}
	}
}

include itemplate('diypage');

?>
