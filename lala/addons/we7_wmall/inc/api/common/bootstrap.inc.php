<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
global $_POST;
mload()->model('store');
mload()->model('order');
load()->func('logging');
$_W['we7_wmall']['config'] = sys_config();

?>
