<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('deliveryer');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '入驻列表';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$agentid = intval($_GPC['agentid']);
	if($agentid > 0) {
		$condition .= ' and agentid = :agentid';
		$params[':agentid'] = $agentid;
	}
	$work_status = intval($_GPC['work_status']);
	if($work_status > 0) {
		$condition .= ' and work_status = :work_status';
		$params[':work_status'] = $work_status;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (title like '%{$keyword}%' or nickname like '%{$keyword}%' or mobile like '%{$keyword}%')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_deliveryer') .  $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	foreach($data as &$row) {
		$row['auth_info'] = iunserializer($row['auth_info']);
	}
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$_W['page']['title'] = '配送员信息';
	$id = intval($_GPC['id']);
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if($_W['ispost']) {
		$mobile = trim($_GPC['mobile']);
		if(!preg_match(IREGULAR_MOBILE, $mobile)) {
			imessage(error(-1, '手机号格式错误'), '', 'ajax');
		}
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':id' => $id));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号已绑定其他配送员, 请更换手机号'), '', 'ajax');
		}
		$openid = trim($_GPC['wechat']['openid']);
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_deliveryer') . ' where uniacid = :uniacid and openid = :openid and id != :id', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':id' => $id));
		if(!empty($is_exist)) {
			imessage(error(-1, '该微信信息已绑定其他配送员, 请更换微信信息'), '', 'ajax');
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'mobile' => $mobile,
			'title' => trim($_GPC['title']),
			'openid' => $openid,
			'nickname' => trim($_GPC['wechat']['nickname']),
			'avatar' => trim($_GPC['wechat']['avatar']),
			'sex' => trim($_GPC['sex']),
			'age' => intval($_GPC['age']),
		);
		if(!$id) {
			$data['password'] = trim($_GPC['password']) ? trim($_GPC['password']) : imessage(error(-1, '登陆密码不能为空'), '', 'ajax');
			$length = strlen($data['password']);
			if($length < 8 || $length > 20) {
				imessage(error(-1, '请输入8-20密码'), referer(), 'ajax');
			}
			if(!preg_match(IREGULAR_PASSWORD, $data['password'])) {
				imessage(error(-1, '密码必须由数字和字母组合'), referer(), 'ajax');
			}
			if($data['password'] != trim($_GPC['repassword'])) {
				imessage(error(-1, '两次密码输入不一致'), referer(), 'ajax');
			}
			$data['salt'] = random(6);
			$data['token'] = random(32);
			$data['password'] = md5(md5($data['salt'] . $data['password']) . $data['salt']);
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_deliveryer', $data);
			$id = pdo_insertid();
			deliveryer_all(true);
			imessage(error(0, '添加配送员成功'), iurl('deliveryer/account/post', array('id' => $id)), 'ajax');
		} else {
			$password = trim($_GPC['password']);
			if(!empty($password)) {
				$length = strlen($password);
				if($length < 8 || $length > 20) {
					imessage(error(-1, '请输入8-20密码'), referer(), 'ajax');
				}
				if(!preg_match(IREGULAR_PASSWORD, $password)) {
					imessage(error(-1, '密码必须由数字和字母组合'), referer(), 'ajax');
				}
				if($password != trim($_GPC['repassword'])) {
					imessage(error(-1, '两次密码输入不一致'), referer(), 'ajax');
				}
				$data['salt'] = random(6);
				$data['password'] = md5(md5($data['salt'].$password) . $data['salt']);
			}
			pdo_update('tiny_wmall_deliveryer', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			deliveryer_all(true);
			imessage(error(0, '编辑配送员成功'), iurl('deliveryer/account/post', array('id' => $id)), 'ajax');
		}
	}
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_delete('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
		pdo_delete('tiny_wmall_deliveryer_current_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
		pdo_delete('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $id));
	}
	deliveryer_all(true);
	imessage(error(0, '删除配送员成功'), '', 'ajax');
}

include itemplate('deliveryer/account');







