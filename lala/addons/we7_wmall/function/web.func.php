<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
function build_menu() {
	global $_W, $_GPC;
	if(defined('IN_PLUGIN')) {
		if(defined('IN_AGENT')) {
			if(defined('IN_AGENT_PLUGIN')) {
				include itemplate("tabs");
			} else {
				include itemplate("{$_W['_controller']}/tabs");
			}
		} else {
			include itemplate('tabs');
		}
	} elseif(defined('IN_MERCHANT')) {
		$file = WE7_WMALL_PATH . "template/web/{$_W['_controller']}/{$_W['_action']}/tabs.html";
		if(is_file($file)) {
			include itemplate("{$_W['_controller']}/{$_W['_action']}/tabs");
		}
	} else {
		include itemplate("{$_W['_controller']}/tabs");
	}
	return true;
}

function imessage($msg, $redirect = '', $type = '') {
	global $_W, $_GPC;
	define('IN_IMESSAGE', 1);
	$_W['page']['title'] = '系统提示';
	if($redirect == 'refresh') {
		$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
	}
	if($redirect == 'referer') {
		$redirect = referer();
	}
	if($redirect == '') {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'info';
	} else {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'success';
	}
	if($_W['isajax'] || !empty($_GET['isajax']) || $type == 'ajax') {
		$vars = array();
		if(is_array($msg)) {
			$msg['url'] = $redirect;
		}
		$vars['message'] = $msg;
		$vars['url'] = $redirect;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}
	if (empty($msg) && !empty($redirect)) {
		header('location: '.$redirect);
	}
	$label = $type;
	if($type == 'error') {
		$label = 'danger';
	}
	if($type == 'ajax' || $type == 'sql') {
		$label = 'warning';
	}
	include itemplate('public/message', TEMPLATE_INCLUDEPATH);
	exit();
}

