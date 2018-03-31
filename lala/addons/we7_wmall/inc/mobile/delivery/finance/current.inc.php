<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$_W['page']['title'] = '账户明细';
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$condition = ' WHERE uniacid = :uniacid AND deliveryer_id = :deliveryer_id';
	$params = array(':uniacid' => $_W['uniacid'], ':deliveryer_id' => $_deliveryer['id']);
	$trade_type = intval($_GPC['trade_type']);

	if (0 < $trade_type) {
		$condition .= ' and trade_type = :trade_type';
		$params[':trade_type'] = $trade_type;
	}

	$id = intval($_GPC['min']);

	if (0 < $id) {
		$condition .= ' and id < :id';
		$params[':id'] = $id;
	}

	$records = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_deliveryer_current_log') . $condition . ' ORDER BY id DESC LIMIT 15', $params, 'id');
	$min = 0;

	if (!empty($records)) {
		foreach ($records as &$row) {
			if ($row['trade_type'] == 1) {
				$row['trade_type_cn'] = '配送费入账';
			}
			else if ($row['trade_type'] == 2) {
				$row['trade_type_cn'] = '申请提现';
			}
			else {
				$row['trade_type_cn'] = '其他变动';
			}

			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
		}

		$min = min(array_keys($records));
	}

	if ($_W['isajax']) {
		$records = array_values($records);
		$respon = array('errno' => 0, 'message' => $records, 'min' => $min);
		imessage($respon, '', 'ajax');
	}

	include itemplate('finance/current');
}

if ($ta == 'detail') {
	$_W['page']['title'] = '交易详情';
	$id = intval($_GPC['id']);
	$current = pdo_get('tiny_wmall_deliveryer_current_log', array('uniacid' => $_W['uniacid'], 'id' => $id));

	if (empty($current)) {
		imessage('交易记录不存在', referer(), 'error');
	}

	if ($current['trade_type'] == 2) {
		$getcash_log = pdo_get('tiny_wmall_deliveryer_getcash_log', array('uniacid' => $_W['uniacid'], 'id' => $current['extra']));
	}

	include itemplate('finance/current');
}

?>
