<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
icheckauth(false);
$activity_id = pdo_fetchcolumn('select activity_id from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and uid = :uid and channel = :channel and type = :type and status = 1 and is_show = 0', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':channel' => 'superRedpacket', ':type' => 'grant'));
if(empty($activity_id)) {
	imessage(error(-1, ''), '', 'ajax');
}
$superRedpacket = pdo_get('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'id' => $activity_id));
if(empty($superRedpacket)) {
	imessage(error(-1, ''), '', 'ajax');
}
$superRedpacket['data'] = json_decode(base64_decode($superRedpacket['data']), true);
$superRedpacket['data']['page']['image'] = tomedia($superRedpacket['data']['page']['image'] );
$redpackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and uid = :uid and channel = :channel and activity_id = :activity_id and status = 1 and is_show = 0', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid'], ':channel' => 'superRedpacket', ':activity_id' => $activity_id));
foreach($redpackets as &$row) {
	$row['discount'] = floatval($row['discount']);
	$row['condition'] = floatval($row['condition']);
	$row['use_days_limit_text'] = date('Y-m-d', $row['starttime']) . '~' . date('Y-m-d', $row['endtime']) . '有效';
}
$data = array(
	'page' => $superRedpacket['data']['page'],
	'redpackets' => $redpackets
);
pdo_update('tiny_wmall_activity_redpacket_record', array('is_show' => 1), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'channel' => 'superRedpacket', 'activity_id' => $activity_id));
imessage(error(0, $data), '', 'ajax');
include itemplate('index');