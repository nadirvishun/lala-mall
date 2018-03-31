<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

if (!in_array($action, array('display', 'post'))) {
	checkwxapp();
}

if (($action == 'version' && $do == 'home') || in_array($action, array('payment', 'module-link-uniacid', 'entrance-link', 'front-download'))) {
	define('FRAME', 'wxapp');
}
