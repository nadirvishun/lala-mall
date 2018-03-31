<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('member');
$_W['page']['title'] = '顾客列表';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$condition = ' where uniacid = :uniacid';
	$params = array(':uniacid' => $_W['uniacid']);
	$key = trim($_GPC['key']);
	if(!empty($key)) {
		$time = strtotime('-30 days');
		if($key == 'success_30') {
			$condition .= ' and success_last_time >= :time';
		} elseif($key == 'noorder_30') {
			$condition .= ' and success_last_time < :time';
		} elseif($key == 'cancel_30') {
			$condition .= ' and cancel_last_time >= :time';
		}
		$params[':time'] = $time;
	}
	$groupid = intval($_GPC['groupid']);
	if($groupid > 0) {
		$condition .= ' and groupid = :groupid';
		$params[':groupid'] = $groupid;
	}
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= ' and (uid = :uid or realname like :keyword or mobile like :keyword or nickname like :keyword)';
		$params[':uid'] = $keyword;
		$params[':keyword'] = "%{$keyword}%";
	}
	$sort = trim($_GPC['sort']);
	$sort_val = intval($_GPC['sort_val']);
	if(!empty($sort)) {
		if($sort_val == 1) {
			$condition .= " ORDER BY {$sort} DESC";
		} else {
			$condition .= " ORDER BY {$sort} ASC";
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_members') . $condition, $params);
	$data = pdo_fetchall('select * from ' . tablename('tiny_wmall_members') . $condition . ' LIMIT '.($pindex - 1) * $psize . ',' . $psize, $params);
	if(!empty($data)) {
		foreach($data as &$val) {
			$groupname = pdo_get('tiny_wmall_member_groups', array('uniacid' => $_W['uniacid'], 'id' => $val['groupid']));
			$member = get_member($val['uid']);
			$val['credit1'] = floatval($member['credit1']);
			$val['credit2'] = $member['credit2'];
			$val['card'] = tosetmeal($val['setmeal_id'],false);
			$val['groupname'] = $groupname['title'];
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$groups = pdo_fetchall('select * from' . tablename('tiny_wmall_member_groups') . 'where uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']));
}

if($op == 'sync') {
	if($_W['isajax']) {
		$uid = intval($_GPC['__input']['uid']);
		$member = pdo_get('tiny_wmall_members', array('uid' => $uid));
		if(!empty($member)) {
			$data = array();
			if(strexists($member['avatar'], "/132132")) {
				$data['avatar'] = str_replace('/132132', '/132', $member['avatar']);
			}
			if($member['sex'] == '1' || $member['sex'] == '2') {
				$data['sex'] = ($member['sex'] == '1' ? '男' : '女');
			}
			pdo_update('tiny_wmall_members', $data, array('uid' => $uid));
		}
		$update = array();
		$update['success_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['success_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and is_pay = 1 and status = 5', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		$update['cancel_price'] = floatval(pdo_fetchcolumn('select sum(final_fee) from ' . tablename('tiny_wmall_order') . ' where uniacid = :uniacid and uid = :uid and status = 6', array(':uniacid' => $_W['uniacid'], ':uid' => $uid)));
		pdo_update('tiny_wmall_members', $update, array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		message(error(0, ''), '', 'ajax');
	}
	$uids = pdo_getall('tiny_wmall_members', array('uniacid' => $_W['uniacid']), array('uid'), 'uid');
	$uids = array_keys($uids);
}

if($op == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_members', array('status' => $status), array('id' => $id, 'uniacid' => $_W['uniacid']));
	imessage(error(0, '修改成功'), referer(), 'ajax');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_members', array('setmeal_endtime' => time()), array('id' => $id, 'uniacid' => $_W['uniacid']));
	imessage(error(0, '套餐取消成功'), referer(), 'ajax');
}

if($op == 'changes') {
	$uid = intval($_GPC['uid']);
	$member = get_member($uid);
	if(empty($member)) {
		imessage(error(-1, '会员不存在或已经删除'), referer(), 'ajax');
	}
	if($_W['ispost']) {
		$type = trim($_GPC['type']);
		$change_type = intval($_GPC['change_type']);
		$amount = floatval($_GPC['amount']);
		$remark= trim($_GPC['remark']);
		$credit = $member['credit1'];
		$credit_text = '积分';
		if($type == 'credit2') {
			$credit = $member['credit2'];
			$credit_text = '余额';
		}
		if($change_type == 1) {
			$amount = '+' . $amount;
		} elseif($change_type == 2) {
			$amount = '-' . $amount;
			if($credit - $amount < 0) {
				$amount = '-' . $credit;
			}
		} elseif($change_type == 3) {
			$amount = $amount - $credit;
		}
		$log = array(
			$member['uid'],
			$remark,
		);
		member_credit_update($member['uid'], $type, $amount, $log);
		imessage(error(0,"{$credit_text}变动成功"), referer(), 'ajax');
	}
	include itemplate('member/op');
	die();
}

if($op == 'setmeal') {
	$id = intval($_GPC['id']);
	$setmeals = pdo_fetchall('select id, uniacid, title from' . tablename('tiny_wmall_delivery_cards') . 'where uniacid = :uniacid',array(':uniacid' => $_W['uniacid']));
	$data = pdo_fetch('select id, uniacid, setmeal_starttime, setmeal_endtime, setmeal_day_free_limit from' . tablename('tiny_wmall_members') . ' where id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
	if($data['setmeal_endtime'] <= TIMESTAMP){
		$data['setmeal_starttime'] = TIMESTAMP;
		$data['setmeal_endtime'] = strtotime("+1 months");
		$data['setmeal_day_free_limit'] = 1;
	}
	if($_W['ispost']) {
		if(empty($id)){
			imessage(error(-1, '请选择需要修改的会员'), referer(), 'ajax');
		}
		$setmeal_day_free_limit = intval($_GPC['free']);
		if(empty($setmeal_day_free_limit)) {
			imessage(error(-1, '每月可享受免费配送次数必须大于0'), referer(), 'ajax');
		}
		pdo_update('tiny_wmall_members', array('setmeal_id' => intval($_GPC['setmeal']), 'setmeal_starttime' => strtotime($_GPC['setmeal_starttime']), 'setmeal_endtime' => strtotime($_GPC['setmeal_endtime']), 'setmeal_day_free_limit' => $_GPC['free']), array('uniacid' => $_W['uniacid'], 'id' => $id));
		imessage(error(0, '套餐修改成功'), referer(), 'ajax');
	}
	include itemplate('member/listOp');
	die();
}

if($op == 'group') {
	$uid = intval($_GPC['uid']);
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), 'groupid');
	$groups = pdo_fetchall('select id, title from' . tablename('tiny_wmall_member_groups') . ' where uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	if($_W['ispost']) {
		if(empty($uid)) {
			imessage(error(-1, '请选择需要修改的会员'), referer(), 'ajax');
		}
		$groupid = intval($_GPC['groupid']);
		if(empty($groupid)) {
			imessage(error(-1, '请选择要修改的会员等级'), referer(), 'ajax');
		}
		pdo_update('tiny_wmall_members', array('groupid' => $groupid), array('uniacid' => $_W['uniacid'], 'uid' => $uid));
		imessage(error(0, '会员等级修改成功'), referer(), 'ajax');
	}
	include itemplate('member/listOp');
	die();
}
include itemplate('member/list');

