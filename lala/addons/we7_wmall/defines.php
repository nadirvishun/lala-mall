<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

!defined('WE7_WMALL_PATH') && define('WE7_WMALL_PATH', IA_ROOT . '/addons/we7_wmall/');
!defined('WE7_WMALL_PLUGIN_PATH') && define('WE7_WMALL_PLUGIN_PATH', WE7_WMALL_PATH . '/plugin/');
!defined('WE7_WMALL_URL') && define('WE7_WMALL_URL', $_W['siteroot'] . 'addons/we7_wmall/');
!defined('WE7_WMALL_STATIC') && define('WE7_WMALL_STATIC', WE7_WMALL_URL . 'static/');
!defined('WE7_WMALL_LOCAL') && define('WE7_WMALL_LOCAL', '../addons/we7_wmall/');
!defined('WE7_WMALL_DEBUG') && define('WE7_WMALL_DEBUG', '1');

define('IREGULAR_EMAIL', '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i');
define('IREGULAR_MOBILE', '/^[01][345678][0-9]{9}$/');
define('IREGULAR_PASSWORD', '/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/');


