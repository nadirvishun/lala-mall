<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
icheckauth();

$check = $_config_plugin['relate']['become_check'];
if($op == 'index') {
	$_W['page']['title'] = '推广员申请';
	$spread = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	if($spread['is_spread'] == 1) {
		if($spread['spread_status'] == 1)  {
			header('location:' . imurl('spread/index'));
			die;
		} elseif($spread['spread_status'] == 2) {
			imessage('您已经被加入到推广员黑名单', '', 'error');
		} elseif($spread['spread_status'] == 0) {
			imessage('您已提交推广员申请,请等待管理员审核', '', 'info');
		}
	}

	$configRelate = $_config_plugin['relate'];
	$configTemplate = $_config_plugin['template'];
	$legal = 0;
	if($configRelate['become'] == 0) {
		$legal = 1;
	} elseif($configRelate['become'] == 1) {
		$legal = 0;
		$protocol = get_config_text("spread:agreement");
	} elseif($configRelate['become'] == 2) {
		$condition = "where uniacid = :uniacid and status = 5 and uid = :uid";
		$params = array(
			':uniacid' => $_W['uniacid'],
			'uid' => $_W['member']['uid'],
		);
		$frquency = pdo_fetchcolumn('select count(*) from' . tablename('tiny_wmall_order'). $condition, $params);
	} elseif($configRelate['become'] == 3) {
		$condition = "where uniacid = :uniacid and status = 5 and uid = :uid";
		$params = array(
			':uniacid' => $_W['uniacid'],
			'uid' => $_W['member']['uid'],
		);
		$price = pdo_fetchcolumn('select sum(final_fee) from ' .tablename('tiny_wmall_order') . $condition, $params);
		$price = round($price, 0);
	}
	if($_W['isajax']) {
		$data = array(
			'is_spread' => 1,
		);
		if($check == 1){
			$data['spread_status'] = 0;
		} else {
			$data['spread_status'] = 1;
			$data['spreadtime'] = TIMESTAMP;
		}
		pdo_update('tiny_wmall_members', $data, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		imessage(error(0, "您已成功提交申请"), imurl('spread/index'), 'ajax');
	}
	if($legal == 1) {
		$update = array(
			'is_spread' => 1,
		);
		if($check == 1){
			$update['spread_status'] = 0;
		} else {
			$update['spread_status'] = 1;
			$update['spreadtime'] = TIMESTAMP;
		}
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		header('location:' . imurl('spread/index'));
		die;
	}
}

if($op == 'application') {
	if($_W['isajax']) {
		$name = trim($_GPC['name']);
		if(empty($name)) {
			imessage(error(-1, '姓名不能为空'), '', 'ajax');
		}
		$mobile = trim($_GPC['mobile']);
		if(empty($mobile)) {
			imessage(error(-1, '手机号不能为空'), '', 'ajax');
		}
		$update = array(
			'realname' => $name,
			'mobile' => $mobile,
			'is_spread' => 1,
		);
		if($check == 1) {
			$update['spread_status'] = 0;
		} else {
			$update['spread_status'] = 1;
			$update['spreadtime'] = TIMESTAMP;
		}
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
		//需要有微信模板消息通知
		if($check == 1) {
			sys_notice_spread_settle($_W['member']['uid'], 'apply');
		} else {
			sys_notice_spread_settle($_W['member']['uid'], 'success');
		}
		imessage(error(0, "您已成功提交申请"), imurl('spread/register'), 'ajax');
	}
}

include itemplate('register');