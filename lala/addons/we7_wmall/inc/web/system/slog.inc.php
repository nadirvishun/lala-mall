<?php
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = ((trim($_GPC['op']) ? trim($_GPC['op']) : 'list'));
if ($op == 'list') 
{
	$_W['page']['title'] = '错误日志';
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$type = trim($_GPC['type']);
	if (!(empty($type))) 
	{
		$condition .= ' and type = :type';
		$params[':type'] = $type;
	}
	$days = ((isset($_GPC['days']) ? intval($_GPC['days']) : -2));
	$starttime = $todaytime = strtotime(date('Y-m-d'));
	$endtime = $todaytime + 86399;
	if (-2 < $days) 
	{
		if ($days == -1) 
		{
			$starttime = strtotime(trim($_GPC['addtime']['start']));
			$endtime = strtotime(trim($_GPC['addtime']['end'])) + 86399;
			$condition .= ' and addtime >= :starttime and addtime <= :endtime';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		else 
		{
			$starttime = strtotime('-' . $days . ' days', $todaytime);
			$condition .= ' and addtime >= :addtime';
			$params[':addtime'] = $starttime;
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_system_log') . $condition, $params);
	$logs = pdo_fetchall('select * from ' . tablename('tiny_wmall_system_log') . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	if (!(empty($logs))) 
	{
		foreach ($logs as &$row ) 
		{
			$row['params'] = json_encode(iunserializer($row['params']));
			$row['message'] = iunserializer($row['message']);
		}
	}
	$pager = pagination($total, $pindex, $psize);
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
		pdo_delete('tiny_wmall_system_log', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	imessage(error(0, '删除错误日志成功'), referer(), 'ajax');
}
if ($op == 'delAll') 
{
	if ($_W['ispost']) 
	{
		pdo_run('TRUNCATE ims_tiny_wmall_system_log');
	}
	imessage(error(0, '删除错误日志成功'), referer(), 'ajax');
}
include itemplate('system/slog');
?>