<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;

if ($_W['is_agent']) {
	mload()->model('agent');
	$_W['agentid'] = intval($_GPC['agentid']);

	if (0 < $_W['agentid']) {
		$_W['agent'] = get_agent($_W['agentid'], array('id', 'area'));
	}

	if ((($_W['agentid'] <= 0) || empty($_W['agent'])) && ($_GPC['ac'] != 'agent')) {
		header('location:' . imurl('errander/agent'));
		exit();
	}

	$agent_config_plugin = get_agent_plugin_config('errander');

	if (!empty($agent_config_plugin)) {
		$_config_plugin = multimerge($_config_plugin, $agent_config_plugin);
	}
}

if (empty($_config_plugin['map']['location_x']) || empty($_config_plugin['map']['location_y'])) {
	$_config_plugin['map'] = $_W['_plugin']['config']['map'] = array('location_x' => '39.90923', 'location_y' => '116.397428');
}

?>
