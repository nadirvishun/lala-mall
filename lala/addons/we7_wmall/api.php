<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
define('IN_IA', true);
define('IN_MOBILE', true);
define('STARTTIME', microtime());
define('IA_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('TIMESTAMP', time());

$_W = $_GPC = array();
$configfile = IA_ROOT . "/data/config.php";

if(!file_exists($configfile)) {
	exit(json_encode(ierror(-1, '获取站点配置文件失败')));
}
require $configfile;
require IA_ROOT . '/framework/version.inc.php';
require IA_ROOT . '/framework/const.inc.php';
require IA_ROOT . '/framework/class/loader.class.php';
load()->func('global');
load()->func('compat');
load()->func('pdo');
load()->classs('account');
load()->model('cache');
load()->model('account');
load()->model('setting');

define('CLIENT_IP', getip());

$_W['config'] = $config;
$_W['config']['db']['tablepre'] = !empty($_W['config']['db']['master']['tablepre']) ? $_W['config']['db']['master']['tablepre'] : $_W['config']['db']['tablepre'];
$_W['timestamp'] = TIMESTAMP;
$_W['charset'] = $_W['config']['setting']['charset'];
$_W['clientip'] = CLIENT_IP;

unset($configfile, $config);

// 附件地址绝对路径
define('ATTACHMENT_ROOT', IA_ROOT .'/attachment/');

define('DEVELOPMENT', $_W['config']['setting']['development'] == 1);
if(DEVELOPMENT) {
	ini_set('display_errors', '1');
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(0);
}
if(!in_array($_W['config']['setting']['cache'], array('mysql', 'file', 'memcache'))) {
	$_W['config']['setting']['cache'] = 'mysql';
}
load()->func('cache');

if(function_exists('date_default_timezone_set')) {
	date_default_timezone_set($_W['config']['setting']['timezone']);
}
if(!empty($_W['config']['memory_limit']) && function_exists('ini_get') && function_exists('ini_set')) {
	if(@ini_get('memory_limit') != $_W['config']['memory_limit']) {
		@ini_set('memory_limit', $_W['config']['memory_limit']);
	}
}

$_W['script_name'] = htmlspecialchars(scriptname());

$sitepath = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
$_W['siteroot'] = htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . $sitepath);
if(substr($_W['siteroot'], -1) != '/') {
	$_W['siteroot'] .= '/';
}
$urls = parse_url($_W['siteroot']);
$urls['path'] = str_replace(array('/web', '/app', '/payment/wechat', '/payment/alipay', '/api'), '', $urls['path']);
$_W['siteroot'] = $urls['scheme'].'://'.$urls['host'].((!empty($urls['port']) && $urls['port']!='80') ? ':'.$urls['port'] : '').$urls['path'];
$_W['siteurl'] = $urls['scheme'].'://'.$urls['host'].((!empty($urls['port']) && $urls['port']!='80') ? ':'.$urls['port'] : '') . $_W['script_name'] . (empty($_SERVER['QUERY_STRING'])?'':'?') . $_SERVER['QUERY_STRING'];

$_W['isajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$_W['ispost'] = $_SERVER['REQUEST_METHOD'] == 'POST';

if(MAGIC_QUOTES_GPC) {
	$_GET = istripslashes($_GET);
	$_POST = istripslashes($_POST);
	$_COOKIE = istripslashes($_COOKIE);
}
$cplen = strlen($_W['config']['cookie']['pre']);
foreach($_COOKIE as $key => $value) {
	if(substr($key, 0, $cplen) == $_W['config']['cookie']['pre']) {
		$_GPC[substr($key, $cplen)] = $value;
	}
}
unset($cplen, $key, $value);

$_GPC = array_merge($_GET, $_POST, $_GPC);
$_GPC = ihtmlspecialchars($_GPC);
if(!$_W['isajax']) {
	$input = file_get_contents("php://input");
	if (!empty($input)) {
		$__input = @json_decode($input, true);
		if (!empty($__input)) {
			$_GPC['__input'] = $__input;
			$_W['isajax'] = true;
		}
	}
	unset($input, $__input);
}

setting_load();
if (empty($_W['setting']['upload'])) {
	$_W['setting']['upload'] = array_merge($_W['config']['upload']);
}
$_W['attachurl'] = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
if (!empty($_W['setting']['remote']['type'])) {
	$_W['attachurl'] = $_W['attachurl_remote'] = ($_W['setting']['remote']['type'] == 1) ? $_W['setting']['remote']['ftp']['url'] . '/' : $_W['setting']['remote']['alioss']['url'].'/';
}
$no_i = intval($_GPC['no_i']);
if(!empty($no_i)) {
	header('Content-Type: text/html; charset=' . $_W['charset']);
}
load()->app('common');
load()->model('mc');
if(empty($no_i)) {
	$_W['uniacid'] = intval($_GPC['i']);
	if(empty($_W['uniacid'])) {
		$_W['uniacid'] = intval($_GPC['weid']);
	}
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	if(empty($_W['uniaccount'])) {
		message(ierror(-1, '指定主公众号不存在'), '', 'ajax');
	}
	$_W['acid'] = $_W['uniaccount']['acid'];
}

$eid = intval($_GPC['eid']);
if(!empty($eid)) {
	$sql = 'SELECT * FROM ' . tablename('modules_bindings') . ' WHERE `eid`=:eid';
	$entry = pdo_fetch($sql, array(':eid' => $eid));
	$_GPC['m'] = $entry['module'];
} else {
	$entry = array(
		'module' => $_GPC['m'],
		'do' => $_GPC['do'],
		'state' => $_GPC['state'],
		'direct' => 0
	);
}
if(empty($entry) || empty($entry['do'])) {
	message(ierror(-1, '非法访问'), '', 'ajax');
}

$_GPC['__entry'] = $entry['title'];
$_GPC['__state'] = $entry['state'];

define('IN_MODULE', $entry['module']);

$site = WeUtility::createModuleSite($entry['module']);

if(!is_error($site)) {
	if(in_array($entry['do'], array('mobile'))) {
		$method = 'doMobile' . ucfirst($entry['do']);
	} else {
		$method = 'doApi' . ucfirst($entry['do']);
	}
	exit($site->$method());
}
exit();


