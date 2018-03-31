<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['_process'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and order_type < 3 and status >= 1 and status <= 4', array(':uniacid' => $_W['uniacid']));
$_W['_remind'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and order_type < 3 and status >= 1 and status <= 4 and is_remind = 1', array(':uniacid' => $_W['uniacid']));
$_W['_refund'] = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and order_type < 3 and refund_status = 1', array(':uniacid' => $_W['uniacid']));

?>
