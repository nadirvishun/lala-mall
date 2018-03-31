<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '详细记录';
$condition = ' where uniacid = :uniacid and uid = :uid';
$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']);
$id = intval($_GPC['min']);

if (0 < $id) {
	$condition .= ' and id < :id';
	$params[':id'] = $id;
}

$records = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_grant_record') . $condition . ' order by id desc limit 15', $params, 'id');
$labels = grant_types();
$min = 0;

if (!empty($records)) {
	foreach ($records as &$row) {
		$row['credittype'] = '积分';

		if ($row['credittype'] == 'credit2') {
			$row['credittype'] = '余额';
		}

		$row['grant'] = floatval($row['grant']);
		$row['css'] = $labels[$row['type']]['css'];
		$row['text'] = $labels[$row['type']]['text'];
		$row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
	}

	$min = min(array_keys($records));
}

if ($_W['isajax']) {
	$records = array_values($records);
	$respon = array('errno' => 0, 'message' => $records, 'min' => $min);
	imessage($respon, '', 'ajax');
}

include itemplate('record');

?>
