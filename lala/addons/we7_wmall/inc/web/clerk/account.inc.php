<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '店员账户';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (title like '%{$keyword}%' or nickname like '%{$keyword}%' or mobile like '%{$keyword}%')";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_clerk') .  $condition, $params);
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_clerk') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

if($op == 'post') {
	$_W['page']['title'] = '添加店员';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}
	if($_W['ispost']) {
		$mobile = trim($_GPC['mobile']);
		if(!preg_match(IREGULAR_MOBILE, $mobile)) {
			imessage(error(-1, '手机号格式错误'), '', 'ajax');
 		}
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':id' => $id));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号已绑定其他店员, 请更换手机号'), '', 'ajax');
		}
		$openid = trim($_GPC['wechat']['openid']);
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_clerk') . ' where uniacid = :uniacid and openid = :openid and id != :id', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':id' => $id));
		if(!empty($is_exist)) {
			imessage(error(-1, '该微信信息已绑定其他店员, 请更换微信信息'), '', 'ajax');
		}

		$data = array(
			'uniacid' => $_W['uniacid'],
			'mobile' => $mobile,
			'title' => trim($_GPC['title']),
			'openid' => $openid,
			'nickname' => trim($_GPC['wechat']['nickname']),
			'avatar' => trim($_GPC['wechat']['avatar']),
		);
		if(!$id) {
			$data['password'] = trim($_GPC['password']) ? trim($_GPC['password']) : imessage(error(-1, '登陆密码不能为空'), '', 'ajax');
			$length = strlen($data['password']);
			if($length < 8 || $length > 20) {
				imessage(error(-1, '请输入8-20密码'), '', 'ajax');
			}
			if(!preg_match(IREGULAR_PASSWORD, $data['password'])) {
				imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
			}
			if($data['password'] != trim($_GPC['repassword'])) {
				imessage(error(-1, '两次密码输入不一致'), '', 'ajax');
			}
			$data['salt'] = random(6);
			$data['password'] = md5(md5($data['salt'] . $data['password']) . $data['salt']);
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_clerk', $data);
			$id = pdo_insertid();
		} else {
			$password = trim($_GPC['password']);
			if(!empty($password)) {
				$length = strlen($password);
				if($length < 8 || $length > 20) {
					imessage(error(-1, '请输入8-20密码'), '', 'ajax');
				}
				if(!preg_match(IREGULAR_PASSWORD, $password)) {
					imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
				}
				if($password != trim($_GPC['repassword'])) {
					imessage(error(-1, '两次密码输入不一致'), '', 'ajax');
				}
				$data['salt'] = random(6);
				$data['password'] = md5(md5($data['salt'].$password) . $data['salt']);
			}
			pdo_update('tiny_wmall_clerk', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		imessage(error(0, '编辑店员成功'), iurl('clerk/account/post', array('id' => $id)), 'ajax');
	}
}

if($op == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		pdo_delete('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'id' => $id));
		pdo_delete('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $id));
	}
	imessage(error(0, '删除店员成功'), '', 'ajax');
}

include itemplate('clerk/account');







