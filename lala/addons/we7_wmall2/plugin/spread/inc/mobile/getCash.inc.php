<?php
/**
 * 外送系统
 * @author 微猫源码
 * @QQ 2058430070
 * @url http://www.weixin2015.cn/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth();
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	$_W['page']['title'] = '提现明细';
	$condition = " where uniacid = :uniacid and spreadid = :spreadid";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':spreadid' => $_W['member']['uid'],
	);
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status > 0) {
		$condition .= " and status = :status";
		$params[':status'] = $status;
	}
	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= " and id < :id";
		$params[':id'] = $id;
	}

	$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_spread_getcash_log') . $condition . ' order by id desc limit 10' , $params, 'id');
	$min = 0;
	if(!empty($records)) {
		foreach($records as &$value) {
			$value['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
		}
		$min = min(array_keys($records));
	}
	if($_W['ispost']) {
		$records = array_values($records);
		$respon = array('errno' => 0, 'message' => $records, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if($op == 'application'){
	$_W['page']['title'] = '申请提现';
	$config = get_plugin_config('spread.settle');
	$member = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
	if($_W['isajax']) {
		$get_fee = floatval($_GPC['fee']);
		if(empty($get_fee)) {
			imessage(error(-1, "提现金额不能为空"), '', 'ajax');
		}
		$channel = trim($_GPC['channel']);
		if(empty($channel)) {
			imessage(error(-1, "请选择佣金提现渠道"), '', 'ajax');
		}
		if(empty($member['openid']) || empty($member['realname'])) {
			imessage(error(-1, "推广员账户信息不完善,无法提现"), '', 'ajax');
		}
		if($get_fee < $config['withdraw']) {
			imessage(error(-1, "提现金额小于最低提现金额限制"), '', 'ajax');
		}
		if($get_fee > $member['spreadcredit2']) {
			imessage(error(-1, "提现金额大于账户可用余额"), '', 'ajax');
		}
		$take_fee = round($get_fee * ($config['withdrawcharge'] / 100), 2);
		$final_fee = $get_fee - $take_fee;
		if($final_fee < 0) {
			$final_fee = 0;
		}
		$openid = mktTransfers_get_openid($_W['member']['uid'], $member['openid'], $get_fee, 'spread');
		if(is_error($openid)) {
			imessage($openid, '', 'ajax');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'spreadid' => $_W['member']['uid'],
			'trade_no' => date("YmdHis") . random(10, true),
			'get_fee' => $get_fee,
			'take_fee' => $take_fee,
			'final_fee' => $final_fee,
			'channel' => $channel,
			'account' => iserializer(
				array(
					'realname' => $member['realname'],
					'openid' => $openid,
					'avatar' => $member['avatar'],
					'nickname' => $member['nickname'],
				)
			),
			'status' => 2,
			'addtime' => TIMESTAMP,
		);
		pdo_insert('tiny_wmall_spread_getcash_log', $data);
		$getcash_id = pdo_insertid();
		$remark = date('Y-m-d H:i:s') . "申请佣金提现,提现金额{$get_fee}元,手续费{$take_fee}元,实际到账{$final_fee}元";
		spread_update_credit2($_W['member']['uid'], -$get_fee, array('trade_type' => 2, 'extra' => $getcash_id, 'remark' => $remark));
		$data = sys_notice_spread_getcash($_W['member']['uid'], $getcash_id, 'apply');
		imessage(error(0, '申请提现成功'), '', 'ajax');
	}
}

include itemplate('getCash');