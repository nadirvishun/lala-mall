<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;

mload()->classs('eleme');

$app = new Eleme();
$url = imurl('wmall/channel/ele', array(), true);
$url = $app->getOauthCodeUrl($url);
if(empty($_GPC['code'])) {
	header('Location: ' . $url);
}
else {
	echo $_GPC['code'];die;
}