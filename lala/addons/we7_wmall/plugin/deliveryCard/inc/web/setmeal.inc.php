<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '套餐列表';

	if ($_W['ispost']) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$title = trim($_GPC['title'][$k]);

				if (empty($title)) {
					continue;
				}

				$data = array('title' => $title, 'displayorder' => intval($_GPC['displayorder'][$k]));
				pdo_update('tiny_wmall_delivery_cards', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}

			imessage(error(0, '修改成功'), iurl('deliveryCard/setmeal/list'), 'ajax');
		}
	}

	$cards = pdo_fetchall('select * from ' . tablename('tiny_wmall_delivery_cards') . ' where uniacid = :uniacid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid']));
}

if ($op == 'post') {
	$_W['page']['title'] = '编辑会员卡套餐';
	$id = intval($_GPC['id']);

	if ($_W['ispost']) {
		$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'days' => intval($_GPC['days']), 'price' => intval($_GPC['price']), 'day_free_limit' => intval($_GPC['day_free_limit']), 'displayorder' => intval($_GPC['displayorder']));

		if (empty($data['title'])) {
			imessage(error(-1, '套餐名称不能为空'), '', 'ajax');
		}

		if (empty($data['days'])) {
			imessage(error(-1, '套餐有效期限不能为空'), '', 'ajax');
		}

		if (0 < $id) {
			pdo_update('tiny_wmall_delivery_cards', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_delivery_cards', $data);
		}

		imessage(error(0, '编辑套餐成功'), iurl('deliveryCard/setmeal/list'), 'ajax');
	}

	if (0 < $id) {
		$card = pdo_get('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $id));

		if (empty($card)) {
			imessage('套餐不存在或已删除', referer(), 'error');
		}
	}
	else {
		$card = array('day_free_limit' => 2);
	}
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_delivery_cards', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除套餐成功'), '', 'ajax');
}

if ($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_delivery_cards', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '设置套餐状态成功'), '', 'ajax');
}

include itemplate('setmeal');

?>
