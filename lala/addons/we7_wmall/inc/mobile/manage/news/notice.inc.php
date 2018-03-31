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
$sid = intval($_GPC['__mg_sid']);

if($ta == 'list') {
	$_W['page']['title'] = '公告列表';
	$condition = ' as b on a.id = b.notice_id where b.uid = :uid and uniacid = :uniacid and type = :type and status = 1';
	$params = array(
		'uniacid' => $_W['uniacid'],
		'uid' => $_W['manager']['id'],
		'type' => 'store',
	);

	$id = intval($_GPC['min']);
	if($id > 0) {
		$condition .= " and a.id < :id";
		$params[':id'] = $id;
	}

	$data = pdo_fetchall('select a.*,b.uid,b.is_new from ' . tablename('tiny_wmall_notice') . ' as a left join' . tablename('tiny_wmall_notice_read_log') . $condition . ' order by id desc, displayorder desc limit 10', $params, 'id');

	$min = 0;
	if(!empty($data)) {
		foreach ($data as &$val) {
			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
		}
		$min = min(array_keys($data));
	}
	if($_W['ispost']) {
		$data = array_values($data);
		$respon = array('errno' => 0, 'message' => $data, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if($ta == 'detail') {
	$_W['page']['title'] = '公告详情';
	$notice = pdo_get('tiny_wmall_notice', array('id' => $_GPC['id'], 'uniacid' => $_W['uniacid'], 'status' => 1, 'type' => 'store'));
	if(empty($notice)) {
		imessage('该消息不存在或已删除', 'manage/news/notice/index', 'error');
	}
	pdo_update('tiny_wmall_notice_read_log', array('is_new' => 0), array('notice_id' => $_GPC['id'], 'uid' => $_W['manager']['id']));
}


include itemplate('news/notice');

