<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$config = get_plugin_config('spread.settle');
if($op == 'index') {
	$_W['page']['title'] = '推广佣金';
	$data = spread_commission_stat();
	$settle = get_plugin_config('spread.settle');
}


include itemplate('commission');