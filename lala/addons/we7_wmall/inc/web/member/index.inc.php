<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('member');
$_W['page']['title'] = '顾客概况';
$start = ($_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m')));
$end = ($_GPC['end'] ? strtotime($_GPC['end']) + 86399 : strtotime(date('Y-m-d')) + 86399);
$day_num = ($end - $start) / 86400;
if ($_W['isajax'] && $_W['ispost']) {
	$days = array();
	$datasets = array(
		'flow1' => array()
		);
	$i = 0;

	while ($i < $day_num) {
		$key = date('m-d', $start + (86400 * $i));
		$days[$key] = 0;
		$datasets['flow1'][$key] = 0;
		++$i;
	}

	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_members') . 'WHERE uniacid = :uniacid AND addtime >= :starttime and addtime <= :endtime', array(':uniacid' => $_W['uniacid'], ':starttime' => $start, 'endtime' => $end));

	foreach ($data as $da) {
		$key = date('m-d', $da['addtime']);

		if (in_array($key, array_keys($days))) {
			++$datasets['flow1'][$key];
		}
	}

	$shuju['label'] = array_keys($days);
	$shuju['datasets'] = $datasets;
	exit(json_encode($shuju));
}

$stat = member_plateform_amount_stat();
include itemplate('member/index');

?>
