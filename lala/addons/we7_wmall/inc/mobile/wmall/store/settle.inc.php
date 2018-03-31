<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '商户入驻';
icheckauth();
$fans = mc_oauth_userinfo();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'account';
$config_store = $_W['we7_wmall']['config']['store'];
$config_store['settle']['agreement'] = get_config_text('agreement_settle');
if($config_store['settle']['status'] != 1) {
	imessage('暂时不支持商户入驻', referer(), 'info');
}
$perm = check_max_store_perm();
if(empty($perm)) {
	imessage('门店入驻量已超过上限,请联系公众号管理员', referer(), 'info');
}
$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
if($ta == 'account') {
	if(!empty($clerk)) {
		header('location:' . imurl('wmall/store/settle/store'));
		die;
	}
	if($_W['isajax']) {
		$mobile = trim($_GPC['mobile']);
		if(!preg_match(IREGULAR_MOBILE, $mobile)) {
			imessage(error(-1, '手机号格式错误'), '', 'ajax');
		}
		if($config_store['settle']['mobile_verify_status'] == 1) {
			$code = trim($_GPC['code']);
			$status = check_verifycode($mobile, $code);
			if(!$status) {
				imessage(error(-1, '验证码错误'), '', 'ajax');
			}
		}
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and mobile = :mobile', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号已绑定其他店员, 请更换手机号'), '', 'ajax');
		}
		$openid = trim($_GPC['openid']) ?  trim($_GPC['openid']) : imessage(error(-1, '微信信息不完善'), '', 'ajax');
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and openid = :openid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if(!empty($is_exist)) {
			imessage(error(-1, '该微信信息已绑定其他店员, 请更换微信信息'),'', 'ajax');
		}
		$password = trim($_GPC['password']) ?  trim($_GPC['password']) : imessage(error(-1, '密码不能为空'), '', 'ajax');
		$length = strlen($password);
		if($length < 8 || $length > 20) {
			imessage(error(-1, '请输入8-20密码'), '', 'ajax');
		}
		if(!preg_match(IREGULAR_PASSWORD, $password)) {
			imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'mobile' => $mobile,
			'title' => trim($_GPC['title']),
			'openid' => $openid,
			'nickname' => trim($_GPC['nickname']),
			'avatar' => trim($_GPC['avatar']),
			'salt' => random(6),
			'addtime' => TIMESTAMP,
		);
		$data['password'] = md5(md5($data['salt'] . $password) . $data['salt']);
		pdo_insert('tiny_wmall_clerk', $data);
		$id = pdo_insertid();
		imessage(error(0, ''), '', 'ajax');
	}
}

if($ta == 'store') {
	if(empty($clerk)) {
		header('location:' . imurl('wmall/store/settle/account'));
		die;
	}
	$store_clerk = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $clerk['id'], 'role' => 'manager'));
	if(!empty($store_clerk)) {
		$store = pdo_get('tiny_wmall_store', array('uniacid' => $_W['uniacid'], 'id' => $store_clerk['sid']));
	}
	if(!empty($store)) {
		if($store['status'] <= 1) {
			imessage('商户入驻申请成功,现在去管理',imurl('manage/home/index'), 'success');
		} else {
			imessage('商户入驻申请正在审核中！', imurl('wmall/member/mine'), 'info');
		}
	}
	if($_W['isajax']) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '商户名称不能为空'), '', 'ajax');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'title' => $title,
			'address' => trim($_GPC['address']),
			'telephone' => trim($_GPC['telephone']),
			'content' => trim($_GPC['content']),
			'status' => $config_store['settle']['audit_status'],
			'business_hours' => iserializer(array(array('s' => '8:00', 'e' => '20:00'))),
			'payment' => iserializer(array('wechat')),
			'remind_time_limit' => 10,
			'remind_reply' => iserializer(array('快递员狂奔在路上,请耐心等待')),
			'addtype' => 2,
			'addtime' => TIMESTAMP,
			'delivery_mode' => $config_store['delivery']['delivery_mode'],
			'delivery_fee_mode' => 1,
			'delivery_price' => $config_store['delivery']['delivery_fee'],
			'push_token' => random(32),
			'self_audit_comment' => intval($config_store['settle']['self_audit_comment']),
		);
		if($config_store['delivery']['delivery_fee_mode'] == 2) {
			$data['delivery_fee_mode'] = 2;
			$data['delivery_price'] = iserializer($data['delivery_price']);
		} else {
			$data['delivery_fee_mode'] = 1;
			$data['delivery_price'] = floatval($data['delivery_price']);
		}
		$delivery_times = get_config_text('takeout_delivery_time');
		$data['delivery_times'] = iserializer($delivery_times);
		pdo_insert('tiny_wmall_store', $data);
		$store_id = pdo_insertid();

		$store_account = array(
			'uniacid' => $_W['uniacid'],
			'agentid' => $_W['agentid'],
			'sid' => $store_id,
			'fee_takeout' => iserializer($config_store['serve_fee']['fee_takeout']),
			'fee_selfDelivery' => iserializer($config_store['serve_fee']['fee_selfDelivery']),
			'fee_instore' => iserializer($config_store['serve_fee']['fee_instore']),
			'fee_paybill' => iserializer($config_store['serve_fee']['fee_paybill']),
			'fee_limit' => $config_store['serve_fee']['get_cash_fee_limit'],
			'fee_rate' => $config_store['serve_fee']['get_cash_fee_rate'],
			'fee_min' => $config_store['serve_fee']['get_cash_fee_min'],
			'fee_max' => $config_store['serve_fee']['get_cash_fee_max'],
		);
		pdo_insert('tiny_wmall_store_account', $store_account);
		$status = pdo_update('tiny_wmall_store_clerk', array('role' => 'manager'), array('uniacid' => $_W['uniacid'], 'clerk_id' => $clerk['id'], 'sid' => $store_id));
		if(empty($status)) {
			pdo_insert('tiny_wmall_store_clerk', array(
				'uniacid' => $_W['uniacid'],
				'sid' => $store_id,
				'clerk_id' => $clerk['id'],
				'role' => 'manager',
				'addtime' => TIMESTAMP,
			));
		}
		sys_notice_settle($store_id, 'clerk', '');
		sys_notice_settle($store_id, 'manager', '');
		imessage(error(0, ''), '', 'ajax');
	}
}

include itemplate('store/settle');