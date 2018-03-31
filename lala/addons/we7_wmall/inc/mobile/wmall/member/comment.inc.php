<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
icheckauth();
$_W['page']['title'] = '我的评论';
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list');

if ($ta == 'list') {
	$comments = pdo_fetchall('select a.id as aid, a.*, b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid order by a.id desc limit 10', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']), 'aid');
	$min = 0;

	if (!empty($comments)) {
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = (($row['delivery_service'] + $row['goods_quality']) / 10) * 100;
			$row['thumbs'] = iunserializer($row['thumbs']);

			if (!empty($row['thumbs'])) {
				foreach ($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
		}

		$min = min(array_keys($comments));
	}
}

if ($ta == 'more') {
	$id = intval($_GPC['min']);
	$comments = pdo_fetchall('select a.id as aid,a.*,b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . ' as b on a.sid = b.id where a.uniacid = :uniacid and a.uid = :uid and a.id < :id order by a.id desc limit 10', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':id' => $id), 'aid');

	if (!empty($comments)) {
		foreach ($comments as &$row) {
			$row['data'] = iunserializer($row['data']);
			$row['score'] = (($row['delivery_service'] + $row['goods_quality']) / 10) * 100;
			$row['addtime_cn'] = date('Y-m-d H:i', $row['addtime']);
			$row['replytime_cn'] = date('Y-m-d H:i', $row['replytime']);
			$row['thumbs'] = iunserializer($row['thumbs']);

			if (!empty($row['thumbs'])) {
				foreach ($row['thumbs'] as &$item) {
					$item = tomedia($item);
				}
			}
		}

		$min = min(array_keys($comments));
	}
	else {
		$min = 0;
	}

	$comments = array_values($comments);
	$respon = array('errno' => 0, 'message' => $comments, 'min' => $min);
	imessage($respon, '', 'ajax');
}

include itemplate('member/comment');

?>
