<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('plugin');
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');

if ($op == 'index') {
	$_W['page']['title'] = '应用信息';

	if ($_W['ispost']) {
		if (!empty($_GPC['ids'])) {
			$statuss = $_GPC['statuss'];

			foreach ($_GPC['ids'] as $k => $v) {
				$status = 0;
				if (!empty($statuss) && in_array($v, $statuss)) {
					$status = 1;
				}

				$data = array('title' => trim($_GPC['titles'][$k]), 'ability' => trim($_GPC['abilitys'][$k]), 'status' => $status, 'displayorder' => intval($_GPC['displayorders'][$k]));
				pdo_update('tiny_wmall_plugin', $data, array('id' => intval($v)));
			}
		}

		imessage(error(0, '修改成功'), 'refresh', 'ajax');
	}

	$condition = ' where 1 and is_show = 1';
	$type = trim($_GPC['type']);

	if (!empty($type)) {
		$condition .= ' and type = :type';
		$params[':type'] = $type;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and (name like :keyword or title like :keyword)';
		$params[':keyword'] = '%' . $keyword . '%';
	}

	$plugins = pdo_fetchall('select * from ' . tablename('tiny_wmall_plugin') . $condition, $params);
	$types = plugin_types();
	include itemplate('system/plugin');
}

?>
