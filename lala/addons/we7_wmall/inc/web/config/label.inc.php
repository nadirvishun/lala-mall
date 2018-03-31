<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('build');
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'TY_store_label');

if ($op == 'TY_store_label') {
	$_W['page']['title'] = '商户标签';

	if ($_W['ispost']) {
		$ids = array(0);

		if (!empty($_GPC['add_title'])) {
			foreach ($_GPC['add_title'] as $k => $v) {
				$title = trim($v);
				$color = trim($_GPC['add_color'][$k]);
				if (empty($title) || empty($color)) {
					continue;
				}

				$insert = array('uniacid' => $_W['uniacid'], 'title' => $title, 'color' => $color, 'displayorder' => intval($_GPC['add_displayorder'][$k]), 'is_system' => 0, 'type' => 'TY_store_label');
				pdo_insert('tiny_wmall_category', $insert);
				$ids[] = pdo_insertid();
			}
		}

		if (!empty($_GPC['id'])) {
			foreach ($_GPC['id'] as $k => $v) {
				$id = intval($v);
				$title = trim($_GPC['title'][$k]);
				$color = trim($_GPC['color'][$k]);
				if (((0 < $id) && empty($title)) || empty($color)) {
					$ids[] = $id;
					continue;
				}

				$update = array('title' => $title, 'color' => $color, 'displayorder' => intval($_GPC['displayorder'][$k]));
				pdo_update('tiny_wmall_category', $update, array('uniacid' => $_W['uniacid'], 'type' => 'TY_store_label', 'id' => $id));
				$ids[] = $id;
			}
		}

		$ids = implode(',', $ids);
		pdo_query('delete from ' . tablename('tiny_wmall_category') . ' where uniacid = ' . $_W['uniacid'] . ' and type = \'TY_store_label\' and is_system = 0 and id not in (' . $ids . ')');
		imessage(error(0, '保存成功'), iurl('config/label/TY_store_label'), 'ajax');
	}

	build_category('TY_store_label');
	$labels = category_store_label();
}

include itemplate('config/label');

?>
