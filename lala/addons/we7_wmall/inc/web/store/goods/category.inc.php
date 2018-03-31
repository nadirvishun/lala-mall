<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'post') {
	$_W['page']['title'] = '编辑商品分类';
	if($_W['ispost']) {
		if(!empty($_GPC['title'])) {
			foreach($_GPC['title'] as $k => $v) {
				$v = trim($v);
				if(empty($v)) continue;
				$data['sid'] = $sid;
				$data['uniacid'] = $_W['uniacid'];
				$data['title'] = $v;
				$data['min_fee'] = intval($_GPC['min_fee'][$k]);
				$data['displayorder'] = intval($_GPC['displayorder'][$k]);
				pdo_insert('tiny_wmall_goods_category', $data);
			}
		}
		imessage(error(0, '添加商品分类成功'), iurl('store/goods/category/list'), 'ajax');
	}
}

if($ta == 'list') {
	$_W['page']['title'] = '分类列表';

	$condition = ' uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE ' . $condition, $params);
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
	if(!empty($lists)) {
		$ids = implode(',', array_keys($lists));
		$nums = pdo_fetchall('SELECT count(*) AS num,cid FROM ' . tablename('tiny_wmall_goods') . " WHERE uniacid = :aid AND cid IN ({$ids}) GROUP BY cid", array(':aid' => $_W['uniacid']), 'cid');
	}
	$pager = pagination($total, $pindex, $psize);
	if($_W['ispost']) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'min_fee' => trim($_GPC['min_fee'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_goods_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			imessage(error(0, '编辑成功'), iurl('store/goods/category/list'), 'ajax');
		}
	}
}

if($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $id));
	imessage(error(0, '删除商品分类成功'), iurl('store/goods/category/list'), 'ajax');
}

if($ta == 'export') {
	$_W['page']['title'] = '批量导入';
	if($_W['ispost']) {
		$file = upload_file($_FILES['file'], 'excel');
		if(is_error($file)) {
			imessage(error(-1, $file['message']), '', 'ajax');
		}
		$data = read_excel($file);
		if(is_error($data)) {
			imessage(error(-1, $data['message']), '', 'ajax');
		}
		unset($data[0]);
		if(empty($data)) {
			imessage(error(-1, '没有要导入的数据'), '', 'ajax');
		}
		foreach($data as $da) {
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'sid' => $sid,
				'title' => trim($da[0]),
				'displayorder' => intval($da[1]),
				'status' =>  intval($da[2]),
			);
			pdo_insert('tiny_wmall_goods_category', $insert);
		}
		imessage(error(0, '导入商品分类成功'), iurl('store/goods/category/list'), 'ajax');
	}
}

if($ta == 'status') {
	if($_W['isajax']) {
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		pdo_update('tiny_wmall_goods_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid , 'id' => $id));
		imessage(error(0, ''), '', 'ajax');
	}
}

include itemplate('store/goods/category');