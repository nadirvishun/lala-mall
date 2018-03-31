<?php
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = ((trim($_GPC['op']) ? trim($_GPC['op']) : 'list'));
if ($op == 'list') 
{
	$_W['page']['title'] = '全部模板';
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => 0);
	$type = intval($_GPC['type']);
	if (0 < $type) 
	{
		$condition .= ' and type = :type';
		$params[':type'] = $type;
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM' . tablename('tiny_wmall_diypage_template') . $condition, $params);
	$templates = pdo_fetchall('select * from ' . tablename('tiny_wmall_diypage_template') . $condition . ' limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
}
if ($op == 'create') 
{
	$id = intval($_GPC['id']);
	$template = pdo_fetch('select * from ' . tablename('tiny_wmall_diypage_template') . ' where (uniacid = 0 or uniacid = :uniacid) and id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if (empty($template)) 
	{
		imessage('模板数据不存在', referer(), 'error');
	}
	$data = array('uniacid' => $_W['uniacid'], 'name' => $template['name'], 'data' => $template['data'], 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_diypage', $data);
	$id = pdo_insertid();
	imessage('初始化模板数据成功,正在进入自定义页面', iurl('diypage/diy/post', array('id' => $id)), 'success');
}
if ($op == 'del') 
{
	$id = intval($_GPC['id']);
	echo $id;
	pdo_delete('tiny_wmall_diypage_template', array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '删除模板成功'), referer(), 'ajax');
}
include itemplate('template');
?>