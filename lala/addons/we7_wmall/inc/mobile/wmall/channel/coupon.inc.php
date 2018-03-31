<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$_W['page']['title'] = '领券中心';
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$id = intval($_GPC['min']);
	$condition = ' where a.type = :type and a.uniacid = :uniacid and a.status = 1';
	$params = array(':type' => 'couponCollect', ':uniacid' => $_W['uniacid']);

	if (0 < $id) {
		$condition .= ' AND a.id < :id';
		$params[':id'] = $id;
	}

	$datas = pdo_fetchall('select a.*,a.id as aid,b.title as store_title,b.logo from ' . tablename('tiny_wmall_activity_coupon') . ' as a left join' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id' . $condition . ' order by a.id desc limit 15', $params, 'aid');

	foreach ($datas as &$row) {
		$row['logo'] = tomedia($row['logo']);
		$row['coupons'] = array_filter(iunserializer($row['coupons']));
		$row['num'] = count($row['coupons']);
		$row['discount'] = 0;

		if (1 < $row['num']) {
			foreach ($row['coupons'] as $cou) {
				$row['discount'] += $cou['discount'];
			}

			$row['couponInfo'] = '内含' . $row['num'] . '张券';
		}
		else {
			$row['coupons'] = array_values($row['coupons']);
			$row['discount'] = $row['coupons'][0]['discount'];
			$row['couponInfo'] = '满' . $row['coupons'][0]['condition'] . '减' . $row['coupons'][0]['discount'];
		}

		$row['get_status'] = 1;
		$record = pdo_get('tiny_wmall_activity_coupon_record', array('uniacid' => $_W['uniacid'], 'couponid' => $row['aid'], 'uid' => $_W['member']['uid']));

		if (!empty($record)) {
			$row['get_status'] = 0;
		}

		$row['percent'] = round(($row['dosage'] / $row['amount']) * 100, 2);
	}

	$min = 0;

	if (!empty($datas)) {
		$min = min(array_keys($datas));
	}

	if ($_W['ispost']) {
		$datas = array_values($datas);
		$respon = array('errno' => 0, 'message' => $datas, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

if ($ta == 'get') {
	mload()->model('coupon');
	$sid = intval($_GPC['sid']);
	$result = coupon_collect($sid);

	if (is_error($result)) {
		imessage($result, '', 'ajax');
	}

	imessage(error(0, '领取优惠券成'), '', 'ajax');
}

$_share = array('title' => '领券中心', 'desc' => '更多好券等你来拿，快来领券中心领取吧', 'imgUrl' => tomedia($_config_mall['logo']), 'link' => imurl('wmall/channel/coupon', array(), true));
include itemplate('channel/coupon');

?>
