<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '商户登录';
isetcookie('__we7_wmall_store', '', 0);
header('location:' . iurl('store/oauth/login'));
exit();

?>
