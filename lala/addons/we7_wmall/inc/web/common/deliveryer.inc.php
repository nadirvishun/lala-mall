<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('deliveryer');
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'all');

if ($op == 'all') {
	$datas = deliveryer_fetchall();
	$datas = array_values($datas);
	message(error(0, $datas), '', 'ajax');
}

?>
