<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');

if ($op == 'index') {
	$_W['page']['title'] = '商品列表';
	$condition = ' where (a.discount_available_total = -1 or a.discount_available_total > 0) and b.status = 1 and a.uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$sid = intval($_GPC['sid']);

	if (0 < $sid) {
		$condition .= ' and a.sid = :sid';
		$params[':sid'] = $sid;
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_activity_bargain') . ' as b on a.bargain_id = b.id' . $condition, $params);
	$bargains = pdo_fetchall('select a.*,b.order_limit,b.goods_limit from ' . tablename('tiny_wmall_activity_bargain_goods') . ' as a left join ' . tablename('tiny_wmall_activity_bargain') . ' as b on a.bargain_id = b.id' . $condition . ' order by a.mall_displayorder desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

	if (!empty($bargains)) {
		foreach ($bargains as &$row) {
			if ($row['discount_total'] == -1) {
				$row['discount_total'] = '无限';
			}

			if ($row['discount_available_total'] == -1) {
				$row['discount_available_total'] = '无限';
			}

			$row['goods'] = pdo_get('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'id' => $row['goods_id']), array('title', 'thumb', 'price'));
			$row['store'] = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $row['sid']), array('title'));
		}
	}

	$pager = pagination($total, $pindex, $psize);
	$stores = pdo_fetchall('select distinct(a.sid),b.title from ' . tablename('tiny_wmall_activity_bargain') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.status = 1 order by a.sid desc', array(':uniacid' => $_W['uniacid']));

	if ($_W['ispost']) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array('mall_displayorder' => intval($_GPC['mall_displayorder'][$k]));
				pdo_update('tiny_wmall_activity_bargain_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}

			imessage(error(0, '排序成功'), referer(), 'ajax');
		}
	}
}

if ($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '商品下架成功'), referer(), 'ajax');
}

include itemplate('goods');

?>
