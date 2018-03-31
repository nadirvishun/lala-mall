<?php
defined('IN_IA') || exit('Access Denied');
mload()->model('cron');
global $_W;
global $_GPC;
$task = cache_read('we7_wmall:task');
if (!(empty($task)) && (TIMESTAMP < $task['expiretime'])) 
{
	exit('process');
}
cache_write('we7_wmall:task', array('expiretime' => TIMESTAMP + 120));
$accounts = pdo_getall('tiny_wmall_config', array());
if (empty($accounts)) 
{
	exit('success');
}
ignore_user_abort();
set_time_limit(0);
foreach ($accounts as &$account ) 
{
	$_W['uniacid'] = $account['uniacid'];
	if (empty($_W['uniacid']) || ($_W['uniacid'] == -1)) 
	{
		continue;
	}
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	if (empty($_W['uniaccount'])) 
	{
		continue;
	}
	$_W['we7_wmall']['config'] = get_system_config();
	cron_order();
}
cache_delete('we7_wmall:task');
exit('success');
?>