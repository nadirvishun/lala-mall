<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('print');
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$_W['page']['title'] = '打印机列表';
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :uniacid AND sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));

	if (!empty($data)) {
		foreach ($data as &$da) {
			if (!empty($da['print_no'])) {
				if (in_array($da['type'], array('feie', '365'))) {
					$da['status_cn'] = print_query_printer_status($da['type'], $da['print_no'], $da['key'], $da['member_code']);
				}
				else {
					$da['status_cn'] = '打印机不支持查询状态';
				}
			}
			else {
				$da['status_cn'] = '未知';
			}
		}
	}

	$types = print_printer_types();
}

if ($ta == 'post') {
	$_W['page']['title'] = '编辑打印机';
	$id = intval($_GPC['id']);

	if (0 < $id) {
		$item = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_printer') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	}

	if (!empty($item)) {
		$item['print_label'] = explode(',', $item['print_label']);
	}
	else {
		$item = array(
			'status'      => 1,
			'print_nums'  => 1,
			'type'        => 'feie',
			'print_label' => array()
			);
	}

	if ($_W['ispost']) {
		$data['uniacid'] = $_W['uniacid'];
		$data['sid'] = $sid;
		$data['type'] = trim($_GPC['type']);
		$data['status'] = intval($_GPC['status']);
		$data['name'] = !empty($_GPC['name']) ? trim($_GPC['name']) : imessage(error(-1, '打印机名称不能为空'), '', 'ajax');
		$data['print_no'] = !empty($_GPC['print_no']) ? trim($_GPC['print_no']) : imessage(error(-1, '机器号不能为空'), '', 'ajax');
		$data['key'] = trim($_GPC['key']);
		$data['api_key'] = trim($_GPC['api_key']);
		$data['member_code'] = trim($_GPC['member_code']);
		if (($data['type'] == 'yilianyun') || ($data['type'] == 'qiyun')) {
			$data['member_code'] = trim($_GPC['userid']);
		}

		$data['print_nums'] = intval($_GPC['print_nums']) ? intval($_GPC['print_nums']) : 1;
		$data['qrcode_type'] = trim($_GPC['qrcode_type']);
		$data['qrcode_link'] = '';
		if (!empty($_GPC['qrcode_link']) && (strexists($_GPC['qrcode_link'], 'http://') || strexists($_GPC['qrcode_link'], 'https://'))) {
			$data['qrcode_link'] = trim($_GPC['qrcode_link']);
		}

		$data['print_header'] = trim($_GPC['print_header']);
		$data['print_footer'] = trim($_GPC['print_footer']);
		$data['is_print_all'] = intval($_GPC['is_print_all']);
		$data['print_label'] = 0;
		if (($_GPC['print_label_type'] == 1) && !empty($_GPC['print_label'])) {
			$print_label = array();

			foreach ($_GPC['print_label'] as $label) {
				if (0 < $label) {
					$print_label[] = $label;
				}
			}

			if (!empty($print_label)) {
				$data['print_label'] = implode(',', $print_label);
			}
		}

		if (!empty($item) && $id) {
			pdo_update('tiny_wmall_printer', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		else {
			pdo_insert('tiny_wmall_printer', $data);
		}

		imessage(error(0, '更新打印机设置成功'), iurl('store/shop/printer/list'), 'ajax');
	}

	$print_labels = pdo_fetchall('select * from ' . tablename('tiny_wmall_printer_label') . ' where uniacid = :uniacid and sid = :sid order by displayorder desc, id asc', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
}

if ($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_printer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除打印机成功'), '', 'ajax');
}

if ($ta == 'label_list') {
	$_W['page']['title'] = '打印标签列表';

	if ($_W['ispost']) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array('title' => trim($_GPC['title'][$k]), 'displayorder' => intval($_GPC['displayorder'][$k]));
				pdo_update('tiny_wmall_printer_label', $data, array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => intval($v)));
			}

			imessage(error(0, '编辑打印标签成功'), iurl('store/shop/printer/label_list'), 'ajax');
		}
	}

	$condition = ' where uniacid = :uniacid and sid = :sid';
	$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_printer_label') . $condition . ' ORDER BY displayorder DESC,id ASC', $params);
}

if ($ta == 'label_post') {
	$_W['page']['title'] = '编辑打印标签';

	if ($_W['ispost']) {
		if (!empty($_GPC['title'])) {
			foreach ($_GPC['title'] as $k => $v) {
				$v = trim($v);

				if (empty($v)) {
					continue;
				}

				$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'title' => $v, 'displayorder' => intval($_GPC['displayorder'][$k]));
				pdo_insert('tiny_wmall_printer_label', $data);
			}
		}

		imessage(error(0, '添加打印标签成功'), iurl('store/shop/printer/label_list'), 'ajax');
	}
}

if ($ta == 'label_del') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_goods', array('print_label' => 0), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'print_label' => $id));
	pdo_delete('tiny_wmall_printer_label', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	imessage(error(0, '删除打印标签成功'), iurl('store/shop/printer/label_list'), 'ajax');
}

include itemplate('store/shop/printer');

?>
