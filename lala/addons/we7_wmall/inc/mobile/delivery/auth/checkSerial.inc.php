<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '输入序列号';
$config_mall = $_W['we7_wmall']['config']['mall'];
include itemplate('auth/checkSerial');

?>
