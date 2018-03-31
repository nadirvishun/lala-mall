<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
if (empty($_config_plugin['map']['location_x']) || empty($_config_plugin['map']['location_y'])) {
	$_config_plugin['map'] = $_W['_plugin']['config']['map'] = array('location_x' => '39.90923', 'location_y' => '116.397428');
}

?>
