<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'list');

if ($op == 'list') {
	$_W['page']['title'] = '会员收货地址';
	$condition = ' where uniacid = :uniacid and type = 1';
	$params = array(':uniacid' => $_W['uniacid']);
	$status = intval($_GPC['status']);

	if (0 < $status) {
		if ($status == 1) {
			$condition .= ' and location_x != :location_x and location_y != :location_y';
		}
		else {
			if ($status == 2) {
				$condition .= ' and (location_x = :location_x or location_y = :location_y)';
			}
		}

		$params[':location_x'] = '';
		$params[':location_y'] = '';
	}

	$uid = intval($_GPC['uid']);

	if (0 < $uid) {
		$condition .= ' and uid = :uid';
		$params[':uid'] = $uid;
	}

	$keyword = trim($_GPC['keyword']);

	if (!empty($keyword)) {
		$condition .= ' and (realname like :keyword or mobile like :keyword)';
		$params[':keyword'] = '%' . $keyword . '%';
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_address') . $condition, $params);
	$addresses = pdo_fetchall('select * from ' . tablename('tiny_wmall_address') . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'del') {
	$ids = $_GPC['id'];

	if (!is_array($ids)) {
		$ids = array($ids);
	}

	foreach ($ids as $id) {
		pdo_delete('tiny_wmall_address', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}

	imessage(error(0, '删除收货地址成功'), '', 'ajax');
}

include itemplate('member/address');

?>
