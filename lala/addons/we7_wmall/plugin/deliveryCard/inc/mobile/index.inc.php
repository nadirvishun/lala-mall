<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'index');
$_W['page']['title'] = '开通会员';
$agreement_card = get_config_text('agreement_card');
pdo_query('delete from ' . tablename('tiny_wmall_delivery_cards_order') . ' where uniacid = :uniacid and is_pay = 0 and addtime < :addtime', array(':uniacid' => $_W['uniacid'], ':addtime' => TIMESTAMP - 3600));
include itemplate('index');

?>
