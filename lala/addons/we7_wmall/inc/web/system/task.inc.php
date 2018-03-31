<?php
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '计划任务';
$url = $_W['siteroot'] . 'app/api.php?&c=entry&ctrl=system&ac=common&op=task&do=mobile&m=we7_wmall&no_i=1';
include itemplate('system/task');
?>