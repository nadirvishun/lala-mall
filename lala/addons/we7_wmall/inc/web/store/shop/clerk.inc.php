<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
mload()->model('clerk');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = '店员列表';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_store_clerk') . 'WHERE uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$data = pdo_fetchall('SELECT *, a.id as aid, a.role as role FROM ' . tablename('tiny_wmall_store_clerk') . 'as a left join' . tablename('tiny_wmall_clerk') . 'as b on a.clerk_id = b.id WHERE a.uniacid = :uniacid and a.sid = :sid ORDER BY aid DESC LIMIT '.($pindex - 1) * $psize. ', '.$psize, array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$pager = pagination($total, $pindex, $psize);
}

if($ta == 'add') {
	if($_W['isajax']) {
		$mobile = trim($_GPC['mobile']);
		if(empty($mobile)) {
			imessage(error(-1, '手机号不能为空'), referer(), 'ajax');
		}
		$clerk = pdo_get('tiny_wmall_clerk', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));
		if(empty($clerk)) {
			imessage(error(-1, '未找到该手机号对应的店员'), referer(), 'ajax');
		}
		$is_exist = pdo_get('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'clerk_id' => $clerk['id'], 'sid' => $sid));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号对应的账户已经是店员, 请勿重复添加'), referer(), 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'clerk_id' => $clerk['id'],
			'sid' => $sid,
			'addtime' => TIMESTAMP,
			'role' => 'clerk',
			'extra' => iserializer(array(
				'accept_wechat_notice' => 1,
				'accept_voice_notice' => 1,
			)),
		);
		pdo_insert('tiny_wmall_store_clerk', $data);
		imessage(error(0, '添加店员成功'), referer(), 'ajax');
	}
}

if($ta == 'manager') {
	$id = intval($_GPC['id']);
	$clerk = pdo_get('tiny_wmall_store_clerk', array('sid' => $sid, 'id' => $id));
	if(!empty($clerk)) {
		$is_exist = pdo_fetch('select * from ' . tablename('tiny_wmall_store_clerk') . ' where uniacid = :uniacid and clerk_id = :clerk_id and role = :role and sid != :sid', array(':uniacid' => $_W['uniacid'], ':clerk_id' => $clerk['clerk_id'], ':role' => 'manager', ':sid' => $sid));
		if(!empty($is_exist)) {
			imessage(error(0, '该店员已经是其他店铺的管理员,一个店员不能同时是多个店铺的管理员'), referer(), 'ajax');
		}
		pdo_update('tiny_wmall_store_clerk', array('role' => 'clerk'), array('uniacid' => $_W['uniacid'], 'sid' => $sid));
		pdo_update('tiny_wmall_store_clerk', array('role' => 'manager'), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		imessage(error(0, '设置店铺管理员成功'), referer(), 'ajax');
	}
	imessage(error(0, '店员信息有误'), referer(), 'ajax');
}

if($ta == 'del') {
	$ids = $_GPC['id'];
	if(!is_array($ids)) {
		$ids = array($ids);
	}
	foreach($ids as $id) {
		$clerk = pdo_get('tiny_wmall_store_clerk', array('id' => $id));
		pdo_delete('tiny_wmall_store_clerk', array('uniacid' => $_W['uniacid'], 'id' => $id, 'sid' => $sid));
	}
	imessage(error(0, '删除店员成功'), referer(), 'ajax');
}

if($ta == 'cover') {
	$_W['page']['title'] = '店员入口';
	$urls = array(
		'wmerchant' => iurl('store/oauth/login', array(), true),
		'register' => imurl('manage/auth/register', array(), true),
		'login' => imurl('manage/auth/login', array(), true),
	);
}

include itemplate('store/shop/clerk');







