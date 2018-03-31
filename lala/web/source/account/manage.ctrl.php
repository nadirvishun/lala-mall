<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->func('file');
load()->model('user');
$dos = array('display', 'delete');
$do = in_array($_GPC['do'], $dos)? $do : 'display';


$_W['page']['title'] = $account_typename . '列表 - ' . $account_typename;
$account_info = permission_user_account_num();

$role_type = in_array($_W['role'], array(ACCOUNT_MANAGE_NAME_FOUNDER, ACCOUNT_MANAGE_NAME_VICE_FOUNDER, ACCOUNT_MANAGE_NAME_OWNER, ACCOUNT_MANAGE_NAME_MANAGER));

if ($do == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$account_table = table('account');
	
	$type_condition = array(
		ACCOUNT_TYPE_APP_NORMAL => array(ACCOUNT_TYPE_APP_NORMAL),
		ACCOUNT_TYPE_OFFCIAL_NORMAL => array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH),
	);
	$account_table->searchWithType($type_condition[ACCOUNT_TYPE]);
	
	$keyword = trim($_GPC['keyword']);
	if (!empty($keyword)) {
		$account_table->searchWithKeyword($keyword);
	}
	
	if(isset($_GPC['letter']) && strlen($_GPC['letter']) == 1) {
		$account_table->searchWithLetter($_GPC['letter']);
	}

	$order = trim($_GPC['order']);
	$account_table->accountUniacidOrder($order);

	$type = trim($_GPC['type']);
	if ($type == 'noconnect') {
		$account_table->searchWithNoconnect();
	}

	$account_table->searchWithPage($pindex, $psize);
	if ($type == 'expire') {
		$list = $account_table->searchAccountList(true);
	} else {
		$list = $account_table->searchAccountList();
	}


	foreach($list as &$account) {
		$account = uni_fetch($account['uniacid']);
		$account['role'] = permission_account_user_role($_W['uid'], $account['uniacid']);
	}

	$total = $account_table->getLastQueryTotal();
	$pager = pagination($total, $pindex, $psize);
	template('account/manage-display' . ACCOUNT_TYPE_TEMPLATE);
}
if ($do == 'delete') {
	$uniacid = intval($_GPC['uniacid']);
	$acid = intval($_GPC['acid']);
	$uid = $_W['uid'];
	$type = intval($_GPC['type']);
		$state = permission_account_user_role($uid, $uniacid);
	if ($state != ACCOUNT_MANAGE_NAME_OWNER && $state != ACCOUNT_MANAGE_NAME_FOUNDER) {
		itoast('无权限操作！', url('account/manage'), 'error');
	}
	if (!empty($acid) && empty($uniacid)) {
		$account = account_fetch($acid);
		if (empty($account)) {
			itoast('子公众号不存在或是已经被删除', '', '');
		}
		$uniaccount = uni_fetch($account['uniacid']);
		if ($uniaccount['default_acid'] == $acid) {
			itoast('默认子公众号不能删除', '', '');
		}
		pdo_update('account', array('isdeleted' => 1), array('acid' => $acid));
		itoast('删除子公众号成功！您可以在回收站中回复公众号', referer(), 'success');
	}
	
	if (!empty($uniacid)) {
		$account = pdo_get('uni_account', array('uniacid' => $uniacid));
		if (empty($account)) {
			itoast('抱歉，帐号不存在或是已经被删除', url('account/manage', array('account_type' => ACCOUNT_TYPE)), 'error');
		}
		$state = permission_account_user_role($uid, $uniacid);
		if($state != ACCOUNT_MANAGE_NAME_FOUNDER && $state != ACCOUNT_MANAGE_NAME_OWNER) {
			itoast('没有该'. ACCOUNT_TYPE_NAME . '操作权限！', url('account/manage', array('account_type' => ACCOUNT_TYPE)), 'error');
		}
		pdo_update('account', array('isdeleted' => 1), array('uniacid' => $uniacid));
		if($_GPC['uniacid'] == $_W['uniacid']) {
			isetcookie('__uniacid', '');
		}
		cache_delete("uniaccount:{$uniacid}");
		cache_delete("unisetting:{$uniacid}");
	}
	itoast('停用成功！，您可以在回收站中恢复', url('account/manage', array('account_type' => ACCOUNT_TYPE)), 'success');
}