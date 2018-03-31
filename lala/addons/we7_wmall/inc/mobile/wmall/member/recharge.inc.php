<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index');

if ($ta == 'index') {
	$_W['page']['title'] = '账户充值';
	$config_recharge = $_W['we7_wmall']['config']['recharge'];

	if ($config_recharge['status'] != 1) {
		imessage('平台暂未开启充值功能', imurl('wmall/member/mine'), 'info');
	}

	pdo_query('delete from ' . tablename('tiny_wmall_member_recharge') . ' where is_pay = 0 and addtime < :time', array(':time' => TIMESTAMP - 86400));
	$recharges = $config_recharge['items'];
}

if ($ta == 'submit') {
	$config_recharge = $_W['we7_wmall']['config']['recharge'];

	if ($config_recharge['status'] != 1) {
		imessage(error(-1, '平台暂未开启充值功能'), '', 'ajax');
	}

	$price = floatval($_GPC['price']);
	if (!$price || ($price < 0)) {
		imessage(error(-1, '充值金额必须大于0'), '', 'ajax');
	}

	$tag = array('credit2' => $price);

	if (!empty($config_recharge['items'])) {
		foreach ($config_recharge['items'] as $item) {
			if ($item['charge'] == $price) {
				if (!empty($item['back']) && !empty($item['type'])) {
					$tag['grant'] = array('type' => $item['type'], 'back' => $item['back']);
				}

				break;
			}
		}
	}

	$data = array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'openid' => $_W['openid'], 'order_sn' => date('YmdHis') . random(6, true), 'type' => 'credit2', 'fee' => $price, 'final_fee' => $price, 'pay_type' => '', 'is_pay' => 0, 'tag' => iserializer($tag), 'addtime' => TIMESTAMP);
	pdo_insert('tiny_wmall_member_recharge', $data);
	$id = pdo_insertid();
	imessage(error(0, $id), '', 'ajax');
}

include itemplate('member/recharge');

?>
