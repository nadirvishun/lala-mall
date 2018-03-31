<?php
//微擎应用 http://www.we7.cc   
function table_category_fetchall($sid)
{
	global $_W;
	$data = pdo_getall('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid), array(), 'id');
	return $data;
}

function table_status()
{
	$data = array(
		array(),
		array('css' => 'label label-default', 'css_block' => 'block-gray', 'text' => '空闲中'),
		array('css' => 'label label-danger', 'css_block' => 'block-red', 'text' => '已开台'),
		array('css' => 'label label-primary', 'css_block' => 'block-primary', 'text' => '已下单'),
		array('css' => 'label label-success', 'css_block' => 'block-success', 'text' => '已支付')
		);
	return $data;
}

function table_fetch($table_id)
{
	global $_W;
	$table = pdo_get('tiny_wmall_tables', array('uniacid' => $_W['uniacid'], 'id' => $table_id));

	if (!empty($table)) {
		$table['category'] = pdo_get('tiny_wmall_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $table['cid']));
	}

	return $table;
}

function table_order_update($table_id, $order_id, $status)
{
	global $_W;
	$status = pdo_update('tiny_wmall_tables', array('order_id' => $order_id, 'status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $table_id));
	return $status;
}

function assign_board_status()
{
	$data = array(
		array(),
		array('css' => 'label label-primary', 'text' => '排队中'),
		array('css' => 'label label-success', 'text' => '已入号'),
		array('css' => 'label label-warning', 'text' => '已过号'),
		array('css' => 'label label-danger', 'text' => '已取消')
		);
	return $data;
}

function assign_board_fetch($id)
{
	global $_W;
	$board = pdo_get('tiny_wmall_assign_board', array('uniacid' => $_W['uniacid'], 'id' => $id));
	return $board;
}

function assign_queue_fetch($id)
{
	global $_W;
	$queue = pdo_get('tiny_wmall_assign_queue', array('uniacid' => $_W['uniacid'], 'id' => $id));
	return $queue;
}

function assign_notice($sid, $id, $status)
{
	global $_W;
	$config = $_W['we7_wmall']['config'];
	$result = error(-1, '通知参数错误');

	if (!empty($config['notice']['wechat']['assign_tpl'])) {
		$store = store_fetch($sid, array('id', 'title'));
		$board = assign_board_fetch($id);

		if (empty($board)) {
			return false;
		}

		$queue = assign_queue_fetch($board['queue_id']);

		if (empty($queue)) {
			return false;
		}

		$board_status = assign_board_status();
		$url = murl('entry', array('m' => 'we7_wmall', 'do' => 'assign', 'sid' => $sid, 'op' => 'goods'), true, true);
		$wait_count = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and id < :id and queue_id = :queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $board['queue_id'], ':id' => $board['id']));
		$createtime = date('Y-m-d H:i', $board['createtime']);

		if ($status == 1) {
			$first = '排号提醒：编号' . $board['number'] . '已成功领号，您可以点击本消息提前点菜，节约等待时间哦';
			$remark = array('门店名称：' . $store['title'], '排队号码：' . $queue['title'] . ' ' . $board['number'], '前面等待：' . $wait_count . '桌', '排队状态：排队中');
		}
		else if ($status == 2) {
			$first = '排号入号提醒：编号' . $board['number'] . '已入号,请您立即前往迎宾台';
			$remark = array('门店名称：' . $store['title'], '排队号码：' . $queue['title'] . ' ' . $board['number'], '排队状态：已入号', '您在' . $store['title'] . '的的排队状态更新为已入号，请您立即前往迎宾台，如果疑问，请联系我们工作人员');
		}
		else if ($status == 3) {
			$first = '排号过号提醒：编号' . $board['number'] . '已过号';
			$remark = array('门店名称：' . $store['title'], '排队号码：' . $queue['title'] . ' ' . $board['number'], '排队状态：已过号', '您在' . $store['title'] . '的的排队状态更新为已过号，如果疑问，请联系我们工作人员');
		}
		else if ($status == 4) {
			$first = '排号取消提醒：编号' . $board['number'] . '已取消';
			$remark = array('门店名称：' . $store['title'], '排队号码：' . $queue['title'] . ' ' . $board['number'], '排队状态：已取消', '您在' . $store['title'] . '的的排队状态更新为已取消，如果疑问，请联系我们工作人员');
		}
		else {
			if ($status == 5) {
				$first = '排号提醒：还需等待' . $wait_count . '桌';
				$remark = array('门店名称：' . $store['title'], '还需等待：' . $wait_count . '桌', '排队号码：' . $queue['title'] . ' ' . $board['number'], '排队状态：' . $board_status[$board['status']]['text']);
			}
		}

		$remark = implode("\n", $remark);
		$send = array(
			'first'    => array('value' => $first, 'color' => '#ff510'),
			'keyword1' => array('value' => $board['number'], 'color' => '#ff510'),
			'keyword2' => array('value' => $createtime, 'color' => '#ff510'),
			'remark'   => array('value' => $remark, 'color' => '#ff510')
			);
		$acc = WeAccount::create();
		$result = $acc->sendTplNotice($board['openid'], $config['notice']['wechat']['assign_tpl'], $send, $url);

		if (is_error($status)) {
			slog('wxtplNotice', '排号状态变动微信通知顾客', $send, $status['message']);
		}
	}

	return $result;
}

function assign_notice_clerk($sid, $id)
{
	global $_W;
	$config = $_W['we7_wmall']['config'];

	if (!empty($config['notice']['wechat']['assign_tpl'])) {
		$store = store_fetch($sid, array('id', 'title'));
		$board = assign_board_fetch($id);

		if (empty($board)) {
			return false;
		}

		$queue = assign_queue_fetch($board['queue_id']);

		if (empty($queue)) {
			return false;
		}

		mload()->model('clerk');
		$clerks = clerk_fetchall($sid);

		if (empty($clerks)) {
			return false;
		}

		$wait_count = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and status = 1 and id < :id and queue_id = :queue_id', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':queue_id' => $board['queue_id'], ':id' => $board['id']));
		$createtime = date('Y-m-d H:i', $board['createtime']);

		if (empty($config['notice']['wechat']['assign_tpl'])) {
			return false;
		}

		$first = '排号提醒：有新的排号，编号' . $board['number'] . '.请登陆后台进行处理';
		$remark = array('门店名称：' . $store['title'], '排队号码：' . $queue['title'] . ' ' . $board['number'], '还需等待：' . $wait_count . '桌');
		$remark = implode("\n", $remark);
		$send = array(
			'first'    => array('value' => $first, 'color' => '#ff510'),
			'keyword1' => array('value' => $board['number'], 'color' => '#ff510'),
			'keyword2' => array('value' => $createtime, 'color' => '#ff510'),
			'remark'   => array('value' => $remark, 'color' => '#ff510')
			);
		$acc = WeAccount::create();

		foreach ($clerks as $clerk) {
			if (empty($clerk['openid'])) {
				continue;
			}

			$status = $acc->sendTplNotice($clerk['openid'], $config['notice']['wechat']['assign_tpl'], $send);

			if (is_error($status)) {
				slog('wxtplNotice', '新排号微信通知平台管理员', $send, $status['message']);
			}
		}
	}

	return $status;
}

function assign_notice_queue($board_id, $queue_id)
{
	global $_W;
	$queue = assign_queue_fetch($queue_id);
	if (!empty($queue) && (0 < $queue['notify_num'])) {
		$boards = pdo_fetchall('select * from ' . tablename('tiny_wmall_assign_board') . ' where uniacid = :uniacid and sid = :sid and queue_id = :queue_id and status = 1 and id > :id limit ' . $queue['notify_num'], array(':uniacid' => $_W['uniacid'], ':sid' => $queue['sid'], ':queue_id' => $queue_id, ':id' => $board_id));

		if (!empty($boards)) {
			foreach ($boards as $board) {
				if (!empty($board['openid'])) {
					assign_notice($queue['sid'], $board['id'], 5);
				}
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
