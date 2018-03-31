<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
global $_POST;
$mobile = trim($_POST['mobile']);
$password = trim($_POST['password']);
if (empty($mobile) || empty($password)) {
	message(ierror(-1, '手机号或密码为空'), '', 'ajax');
}

$deliveryer = deliveryer_login($mobile, $password);
message($deliveryer, '', 'ajax');

?>
