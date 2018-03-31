<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_GPC;
global $_W;
icheckauth();
mload()->model('redPacket');
redPacket_cron();
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');
$_W['page']['title'] = '我的红包';

if ($ta == 'list') {
	$id = intval($_GPC['min']);
	$condition = ' where uniacid = :uniacid and uid = :uid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':uid'] = $_W['member']['uid'];

	if (0 < $id) {
		$condition .= ' and id < :id';
		$params[':id'] = $id;
	}

	$status = (intval($_GPC['status']) ? intval($_GPC['status']) : 1);

	if (0 < $status) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}

	$redPackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_redpacket_record') . $condition . ' order by id desc limit 15', $params, 'id');
	$min = 0;

	if (!empty($redPackets)) {
		foreach ($redPackets as &$row) {
			$row['mobile'] = $_W['member']['mobile'];
			$row['starttime'] = date('Y-m-d', $row['starttime']);
			$row['endtime'] = date('Y-m-d', $row['endtime']);
			$row['time_cn'] = totime($row['times_limit']);

			if (!empty($row['time_cn'])) {
				$row['time_cn'] = '仅限' . $row['time_cn'] . '时段使用';
			}

			$row['category_cn'] = tocategory($row['category_limit']);

			if (!empty($row['category_cn'])) {
				$row['category_cn'] = '仅限' . $row['category_cn'] . '分类使用';
			}
		}

		$min = min(array_keys($redPackets));
	}

	if ($_W['isajax']) {
		$redPackets = array_values($redPackets);
		$respon = array('errno' => 0, 'message' => $redPackets, 'min' => $min);
		imessage($respon, '', 'ajax');
	}
}

include itemplate('member/redPacket');

?>
