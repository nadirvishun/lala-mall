<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op'])? trim($_GPC['op']): 'list';

if($op == 'list') {
	$_W['page']['title'] = '超级红包列表';
	$condition = ' where uniacid = :uniacid and type = :type';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':type' => 'share'
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and name like '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_superredpacket') .  $condition, $params);
	$activitys = pdo_fetchall('select * from ' . tablename('tiny_wmall_superredpacket') . $condition . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	include itemplate('shareList');
}

if($op == 'post') {
	$_W['page']['title'] = '编辑分享超级红包';
	$id = intval($_GPC['id']);
	if(empty($id)) {
		$superRedpacket_yes = pdo_fetch('select id from ' . tablename('tiny_wmall_superredpacket') . ' where uniacid = :uniacid and type = :type and status = 1', array(':uniacid' => $_W['uniacid'], ':type' => 'share'));
		if(!empty($superRedpacket_yes)) {
			imessage('已有分享超级红包活动, 如需重新添加分享超级红包活动，请先撤销其他活动', referer(), 'info');
		}
	} else {
		$superRedpacket = pdo_get('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
	}
	if($_W['ispost']) {
		$data = $_GPC['data'];
		$startime = strtotime($data['activity']['starttime']);
		$endtime = strtotime($data['activity']['endtime']);
		if($startime >= $endtime) {
			imessage(error(-1, '活动的开始时间不能大于结束时间'), '', 'ajax');
		}
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'name' => $data['activity']['name'],
			'type' => 'share',
			'data' => base64_encode(json_encode($data)),
			'condition' => floatval($data['activity']['condition']),
			'starttime' => $startime,
			'endtime' => $endtime,
			'addtime' => TIMESTAMP
		);
		if($id > 0) {
			pdo_update('tiny_wmall_superredpacket', $insert, array('uniacid' => $_W['uniacid'], 'id' => $id));
			$activity_id = $id;
			pdo_delete('tiny_wmall_superredpacket_share', array('uniacid' => $_W['uniacid'], 'activity_id' => $activity_id));
		} else {
			$insert['status'] = intval($data['activity']['status']);
			pdo_insert('tiny_wmall_superredpacket', $insert);
			$activity_id = pdo_insertid();
		}
		foreach($data['redpackets'] as $row) {
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'activity_id' => $activity_id,
				'title' => trim($row['name']),
				'discount' => floatval($row['discount']),
				'condition' => floatval($row['condition']),
				'grant_days_effect' => intval($row['grant_days_effect']),
				'use_days_limit' => intval($row['use_days_limit']),
				'nums' => intval($row['nums'])
			);
			$times_limit = array();
			if(!empty($row['times'])) {
				foreach($row['times'] as $time) {
					if($time['start_hour'] && $time['end_hour']) {
						$times_limit[] = $time;
					}
				}
			}
			if(!empty($times_limit)) {
				$insert['times_limit'] = iserializer($times_limit);
			}
			$categorys = array();
			if(!empty($row['categorys'])) {
				foreach($row['categorys'] as $val) {
					$categorys[] = $val['id'];
				}
				$insert['category_limit'] = implode('|', $categorys);
			}
			pdo_insert('tiny_wmall_superredpacket_share', $insert);
		}
		imessage(error(0, '设置分享超级红包活动成功'), iurl('superRedpacket/share/list'), 'ajax');
	}
	include itemplate('sharePost');
}

if($op == 'cancel') {
	$id = intval($_GPC['id']);
	pdo_update('tiny_wmall_superredpacket', array('status' => 2), array('uniacid' => $_W['uniacid'], 'id' => $id));
	imessage(error(0, '撤销成功'), referer(), 'ajax');
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'id' => $id));
	pdo_delete('tiny_wmall_superredpacket_grant', array('uniacid' => $_W['uniacid'], 'activity_id' => $id));
	pdo_delete('tiny_wmall_superredpacket_share', array('uniacid' => $_W['uniacid'], 'activity_id' => $id));
	imessage(error(0, '删除成功'), referer(), 'ajax');
}

if($op == 'records') {
	$_W['page']['title'] = '分享记录';
	$activity_id = intval($_GPC['activity_id']);
	$condition = ' where a.uniacid = :uniacid and a.activity_id = :activity_id';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':activity_id' => $activity_id
	);
	$keyword = trim($_GPC['keyword']);
	if(!empty($keyword)) {
		$condition .= " and (b.nickname like '%{$keyword}%' or b.mobile like '%{$keyword}%' or b.uid like '%{$keyword}%')";
	}
	if(!empty($_GPC['addtime'])) {
		$starttime = strtotime($_GPC['addtime']['start']);
		$endtime = strtotime($_GPC['addtime']['end']) + 86399;
	} else {
		$starttime = strtotime('-7 day');
		$endtime = TIMESTAMP;
	}
	$condition .= " and a.addtime > :start and a.addtime < :end";
	$params[':start'] = $starttime;
	$params[':end'] = $endtime;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_superredpacket_grant') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid '. $condition, $params);
	$records = pdo_fetchall('select a.*,b.nickname,b.avatar from ' . tablename('tiny_wmall_superredpacket_grant') . ' as a left join ' . tablename('tiny_wmall_members') . ' as b on a.uid = b.uid '. $condition . ' order by a.id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	include itemplate('shareRecords');
}

if($op == 'redpackets') {
	$_W['page']['title'] = '红包列表';
	$activity_id = intval($_GPC['activity_id']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('tiny_wmall_superredpacket') .  $condition, $params);
	$redpackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_superredpacket_share') . ' where uniacid = :uniacid and activity_id = :activity_id order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':activity_id' => $activity_id));
	$pager = pagination($total, $pindex, $psize);
	include itemplate('shareRedpackets');
}


