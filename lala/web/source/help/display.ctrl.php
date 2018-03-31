<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
global $_W;
load()->model('user');

$_W['page']['title'] = '帮助系统';
if ($_W['ishttps']) {
	header("Content-Security-Policy: upgrade-insecure-requests");
}
template('help/display');