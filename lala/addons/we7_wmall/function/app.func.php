<?php
//微擎应用 http://www.we7.cc   
function imessage($msg, $redirect = '', $type = '')
{
	global $_W;
	global $_GPC;
	define('IN_IMESSAGE', 1);
	$_W['page']['title'] = '系统提示';
	$title = $msg;

	if (is_array($msg)) {
		$message = (isset($msg['']) ? $msg['message'] : '');
		$title = (isset($msg['title']) ? $msg['title'] : '');
		$btn_text = (isset($msg['btn_text']) ? $msg['btn_text'] : '');
	}

	if ($redirect == 'refresh') {
		$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
	}
	else if ($redirect == 'referer') {
		$redirect = referer();
	}
	else {
		if ($redirect == 'close') {
			$redirect = 'javascript:;';
			$close = true;
		}
	}

	if ($redirect == '') {
		$type = (in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'info');
	}
	else {
		$type = (in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'success');
	}

	if ($_W['isajax'] || !empty($_GET['isajax']) || ($type == 'ajax')) {
		$vars = array();

		if (is_array($msg)) {
			$msg['url'] = $redirect;
		}

		$vars['message'] = $msg;
		$vars['url'] = $redirect;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}

	$label = $type;

	if ($type == 'error') {
		$label = 'danger';
	}

	if (($type == 'ajax') || ($type == 'sql')) {
		$label = 'warning';
	}

	include itemplate('public/message', TEMPLATE_INCLUDEPATH);
	exit();
}

function get_mall_menu()
{
	global $_W;
	global $_GPC;
	$file = 'public/nav';

	if (check_plugin_perm('diypage')) {
		$key = 'takeout';

		if ($_W['_controller'] == 'errander') {
			$key = 'errander';
		}
		else {
			if ($_W['_controller'] == 'ordergrant') {
				$key = 'ordergrant';
			}
		}

		$config_menu = get_plugin_config('diypage.menu');
		if (is_array($config_menu) && !empty($config_menu[$key])) {
			$id = intval($config_menu[$key]);
			$temp = pdo_get('tiny_wmall_diypage_menu', array('uniacid' => $_W['uniacid'], 'id' => $id));

			if (!empty($temp)) {
				$menu = json_decode(base64_decode($temp['data']), true);
				$file = 'diypage/menu';
			}
		}
	}

	include itemplate($file, TEMPLATE_INCLUDEPATH);
	return true;
}

function menu_active($url)
{
	global $_W;
	global $_GPC;
	if (strexists($url, 'http://') || strexists($url, 'https://') || empty($url)) {
		return NULL;
	}

	$ctrl = trim($_GPC['c']);
	$action = trim($_GPC['ac']);
	parse_str($url, $urls);
	$_router = implode('/', array($urls['ctrl'], $urls['ac'], $urls['op']));

	if ($_W['_router'] == $_router) {
		return 'active';
	}
}

function get_mall_danmu()
{
	global $_W;
	global $_GPC;

	if (check_plugin_perm('diypage')) {
		$config_danmu = get_plugin_config('diypage.danmu');
		if (is_array($config_danmu) && ($config_danmu['params']['status'] == 1)) {
			$file = 'diypage/danmu';
			include itemplate($file, TEMPLATE_INCLUDEPATH);
		}
	}

	return true;
}

function get_mall_superRedpacket()
{
	global $_W;
	global $_GPC;
	icheckauth(false);

	if (check_plugin_perm('superRedpacket')) {
		$activity_id = pdo_fetchcolumn('select activity_id from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and uid = :uid and channel = :channel and status = 1 and is_show = 0', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':channel' => 'superRedpacket'));

		if (0 < $activity_id) {
			$file = 'superRedpacket/index';
			include itemplate($file, TEMPLATE_INCLUDEPATH);
		}
	}

	return true;
}

function iregister_jssdk($debug = false)
{
	global $_W;

	if (defined('HEADER')) {
		echo '';
		return NULL;
	}

	$sysinfo = array(
		'uniacid'   => $_W['uniacid'],
		'acid'      => $_W['acid'],
		'siteroot'  => $_W['siteroot'],
		'siteurl'   => $_W['siteurl'],
		'attachurl' => $_W['attachurl'],
		'cookie'    => array('pre' => $_W['config']['cookie']['pre'])
		);

	if (!empty($_W['acid'])) {
		$sysinfo['acid'] = $_W['acid'];
	}

	if (!empty($_W['openid'])) {
		$sysinfo['openid'] = $_W['openid'];
	}

	if (defined('MODULE_URL')) {
		$sysinfo['MODULE_URL'] = MODULE_URL;
	}

	$sysinfo = json_encode($sysinfo);
	$jssdkconfig = json_encode($_W['account']['jssdkconfig']);
	$debug = ($debug ? 'true' : 'false');
	$script = "<script src=\"https://res.wx.qq.com/open/js/jweixin-1.0.0.js\"></script>\r\n<script type=\"text/javascript\">\r\n\twindow.sysinfo = window.sysinfo || " . $sysinfo . " || {};\r\n\t// jssdk config 对象\r\n\tjssdkconfig = " . $jssdkconfig . " || {};\r\n\t// 是否启用调试\r\n\tjssdkconfig.debug = " . $debug . ";\r\n\tjssdkconfig.jsApiList = [\r\n\t\t'checkJsApi',\r\n\t\t'onMenuShareTimeline',\r\n\t\t'onMenuShareAppMessage',\r\n\t\t'onMenuShareQQ',\r\n\t\t'onMenuShareWeibo',\r\n\t\t'hideMenuItems',\r\n\t\t'showMenuItems',\r\n\t\t'hideAllNonBaseMenuItem',\r\n\t\t'showAllNonBaseMenuItem',\r\n\t\t'translateVoice',\r\n\t\t'startRecord',\r\n\t\t'stopRecord',\r\n\t\t'onRecordEnd',\r\n\t\t'playVoice',\r\n\t\t'pauseVoice',\r\n\t\t'stopVoice',\r\n\t\t'uploadVoice',\r\n\t\t'downloadVoice',\r\n\t\t'chooseImage',\r\n\t\t'previewImage',\r\n\t\t'uploadImage',\r\n\t\t'downloadImage',\r\n\t\t'getNetworkType',\r\n\t\t'openLocation',\r\n\t\t'getLocation',\r\n\t\t'hideOptionMenu',\r\n\t\t'showOptionMenu',\r\n\t\t'closeWindow',\r\n\t\t'scanQRCode',\r\n\t\t'chooseWXPay',\r\n\t\t'openProductSpecificView',\r\n\t\t'addCard',\r\n\t\t'chooseCard',\r\n\t\t'openCard'\r\n\t];\r\n\twx.config(jssdkconfig);\r\n</script>";
	echo $script;
}

defined('IN_IA') || exit('Access Denied');

?>
