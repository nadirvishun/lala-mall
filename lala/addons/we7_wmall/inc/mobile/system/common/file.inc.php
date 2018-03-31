<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$ta = (trim($_GPC['ta']) ? trim($_GPC['ta']) : 'image');

if ($ta == 'image') {
	$media_id = trim($_GPC['media_id']);
	$status = media_id2url($media_id);

	if (is_error($status)) {
		message($status, '', 'ajax');
	}

	$data = array('errno' => 0, 'message' => $status, 'url' => tomedia($status));
	message($data, '', 'ajax');
}

?>
