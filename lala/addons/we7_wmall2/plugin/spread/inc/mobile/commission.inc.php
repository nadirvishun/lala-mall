<?php

//微猫源码  QQ：2058430070
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$op = ((trim($_GPC['op']) ? trim($_GPC['op']) : 'index'));
if ($op == 'index') 
{
	$_W['page']['title'] = '推广佣金';
	$data = spread_commission_stat();
	$settle = get_plugin_config('spread.settle');
}
include itemplate('commission');
?>