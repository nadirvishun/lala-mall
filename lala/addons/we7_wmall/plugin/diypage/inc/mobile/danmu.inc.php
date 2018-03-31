<?php
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$config_danmu = get_plugin_config('diypage.danmu');
if (!(is_array($config_danmu)) || !($config_danmu['params']['status'])) 
{
	imessage(error(-1, ''), '', 'ajax');
}
if ($config_danmu['params']['dataType'] == 0) 
{
	$members = pdo_fetchall('select nickname, avatar from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and nickname != \'\' and avatar != \'\' order by id desc limit 10;', array(':uniacid' => $_W['uniacid']));
}
else 
{
	$members = pdo_fetchall('select b.nickname, b.avatar from ' . tablename('tiny_wmall_order') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid where a.uniacid = :uniacid and b.nickname != \'\' and b.avatar != \'\' order by a.id desc limit 10;', array(':uniacid' => $_W['uniacid']));
}
if (!(empty($members))) 
{
	foreach ($members as &$val ) 
	{
		$val['avatar'] = tomedia($val['avatar']);
		$val['time'] = mt_rand($config_danmu['params']['starttime'], $config_danmu['params']['endtime']);
		if ($val['time'] <= 0) 
		{
			$val['time'] = '刚刚';
		}
		else if ((0 < $val['time']) && ($val['time'] < 60)) 
		{
			$val['time'] = $val['time'] . '秒前';
		}
		else if (60 < $val['time']) 
		{
			$val['time'] = floor($val['time'] / 60);
			$val['time'] = $val['time'] . '分钟前';
		}
	}
}
imessage(error(0, $members), '', 'ajax');
?>