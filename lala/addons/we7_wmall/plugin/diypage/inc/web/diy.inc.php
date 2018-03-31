<?php
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = ((trim($_GPC['op']) ? trim($_GPC['op']) : 'list'));
if ($op == 'list') 
{
	$_W['page']['title'] = '自定义页面';
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$keyword = trim($_GPC['keyword']);
	if (!(empty($keyword))) 
	{
		$condition .= ' and name like \'%' . $keyword . '%\'';
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_diypage') . $condition, $params);
	$pages = pdo_fetchall('select * from ' . tablename('tiny_wmall_diypage') . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}
if ($op == 'post') 
{
	$_W['page']['title'] = '新建自定义页面';
	$id = intval($_GPC['id']);
	if (0 < $id) 
	{
		$_W['page']['title'] = '编辑自定义页面';
		$page = get_diypage_diy($id);
	}
	$diymenus = diypage_menus();
	if ($_W['ispost']) 
	{
		$data = $_GPC['data'];
		$diydata = array('uniacid' => $_W['uniacid'], 'name' => $data['page']['name'], 'type' => 1, 'diymenu' => $data['page']['diymenu'], 'data' => base64_encode(json_encode($data)), 'updatetime' => TIMESTAMP);
		if (!(empty($id))) 
		{
			pdo_update('tiny_wmall_diypage', $diydata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		else 
		{
			$diydata['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_diypage', $diydata);
			$id = pdo_insertid();
		}
		imessage(error(0, '编辑成功'), iurl('diypage/diy/post', array('id' => $id)), 'ajax');
	}
	if (!(empty($id))) 
	{
		$menu = diypage_menu($id);
	}
}
if ($op == 'del') 
{
	$ids = $_GPC['id'];
	if (!(is_array($ids))) 
	{
		$ids = array($ids);
	}
	foreach ($ids as $id ) 
	{
		pdo_delete('tiny_wmall_diypage', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除自定义页面成功'), referer(), 'ajax');
}
include itemplate('diy');
?>