<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list'){
	$_W['page']['title'] = '商户账户明细';
	$sid = intval($_GPC['__mg_sid']);
	$condition = ' WHERE uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$trade_type = intval($_GPC['trade_type']);
	if($trade_type > 0) {
		$condition .= ' AND trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}
	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= " AND id < :id";
		$params[':id'] = $id;
	}
	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_current_log') . $condition . ' ORDER BY id DESC limit 15', $params, 'id');
	$min = 0;
	if(!empty($records)){
		$min = min(array_keys($records));
	}
	if($_W['ispost']) {
		$records = array_values($records);
		foreach($records as &$record){
			$record['addtime_cn'] = date('Y-m-d H:i',$record['addtime']);
		}
		$respon = array('errno' => 0, 'message' => $records, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
	include itemplate('account/log');
}

if($ta == 'detail') {
	$_W['page']['title'] = '交易详情';
	$condition = ' WHERE uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	$id = intval($_GPC['id']);
	if(!empty($id)) {
		$condition .= ' AND id = :id';
		$params[':id'] = $id;
		$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_store_getcash_log') . $condition, $params);
		$record = $records[0];
		$record['account'] = iunserializer($record['account']);
	}
	include itemplate('account/detail');
}

