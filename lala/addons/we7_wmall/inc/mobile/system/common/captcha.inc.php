<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
load()->classs('captcha');
session_start();
$captcha = new Captcha();
$captcha->build(150, 40);
$hash = md5(strtolower($captcha->phrase) . $_W['config']['setting']['authkey']);
isetcookie('__code', $hash);
$_SESSION['__code'] = $hash;
$captcha->output();

?>
