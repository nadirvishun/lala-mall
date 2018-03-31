<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->func('tpl.app');
$_W['page']['title'] = '举报商户';
icheckauth();
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$title = '举报商家';
	$sid = intval($_GPC['sid']);
	$store = store_fetch($sid, array('title', 'id'));

	if (empty($store)) {
		imessage('门店不存在或已删除', referer(), 'error');
	}

	$reports = $_W['we7_wmall']['config']['report'];
}

if ($ta == 'post') {
	$title = (!empty($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '投诉类型有误'), '', 'ajax'));
	$data = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'sid' => intval($_GPC['sid']), 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'title' => $title, 'note' => trim($_GPC['note']), 'mobile' => trim($_GPC['mobile']), 'addtime' => TIMESTAMP);
	$thumbs = array();

	if (!empty($_GPC['thumbs'])) {
		foreach ($_GPC['thumbs'] as $row) {
			if (empty($row)) {
				continue;
			}

			$thumbs[] = $row;
		}

		$data['thumbs'] = iserializer($thumbs);
	}

	pdo_insert('tiny_wmall_report', $data);
	imessage(error(0, '投诉成功'), '', 'ajax');
}

include itemplate('store/report');

?>
