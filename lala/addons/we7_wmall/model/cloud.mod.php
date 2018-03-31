<?php
defined('IN_IA') || exit('Access Denied');
function cloud_w_request($url, $post = '', $extra = array(), $timeout = 60) 
{
	load()->func('communication');
	$response = ihttp_request($url, $post, $extra, $timeout);
	if (is_error($response)) 
	{
		return error(-1, '错误: ' . $response['message']);
	}
	return $response['content'];
}
function cloud_w_plugins() 
{
	global $_W;
	$plugins = pdo_getall('tiny_wmall_plugin', array(), array('name'), 'name');
	$plugins = array_keys($plugins);
	return $plugins;
}
function cloud_w_query_auth($code, $module) 
{
	global $_W;
	$plugins = cloud_w_plugins();
	$params = array('url' => rtrim($_W['siteroot'], '/'), 'host' => $_SERVER['HTTP_HOST'], 'code' => $code, 'site_id' => $_W['setting']['site']['key'], 'module' => $module, 'method' => 'query_auth', 'uniacid' => $_W['uniacid'], 'plugins' => $plugins);
	$content = cloud_w_request('', $params);
	$result = @json_decode($content, true);
	if (empty($result['message'])) 
	{
		return error(-1, '未知错误');
	}
	return $result['message'];
}
function cloud_w_client_define() 
{
	return array('/model/cloud.mod.php');
}


function __secure_decode($post) 
{
	
}

function cloud_store_url() 
{

}
?>