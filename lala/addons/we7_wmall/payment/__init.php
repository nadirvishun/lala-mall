<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
function get_system_config($key = '') {
	global $_W;
	$config = pdo_get('tiny_wmall_config', array('uniacid' => $_W['uniacid']), array('sysset', 'id'));
	if(empty($config)) {
		if(empty($_config['id'])) {
			$init_config = array(
				'uniacid' => $_W['uniacid']
			);
			pdo_insert('tiny_wmall_config', $init_config);
		}
		return array();
	}
	$sysset = iunserializer($config['sysset']);
	if(!is_array($sysset)) {
		$sysset = array();
	}
	if(empty($key)) {
		return $sysset;
	}
	$keys = explode('.', $key);
	$counts = count($key);
	if($counts == 1) {
		return $sysset[$key];
	} elseif($counts == 2) {
		return $sysset[$keys[0]][$keys[1]];
	} elseif($counts == 3) {
		return $sysset[$keys[0]][$keys[1]][$keys[1]];
	}
}

function get_plugin_config($key = '') {
	global $_W;
	$config = pdo_get('tiny_wmall_config', array('uniacid' => $_W['uniacid']), array('pluginset'));
	if(empty($config)) {
		return array();
	}
	$pluginset = iunserializer($config['pluginset']);
	if(!is_array($pluginset)) {
		return array();
	}
	if(empty($key)) {
		return $pluginset;
	}
	$keys = explode('.', $key);
	$plugin = $keys[0];
	if(!empty($plugin)) {
		$config_plugin = $pluginset[$plugin];
		if(!is_array($config_plugin)) {
			return array();
		}
		if(!empty($keys[1])) {
			return $config_plugin[$keys[1]];
		}
		return $config_plugin;
	}
}
