<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');

if (empty($_W['clerk']['id'])) {
	exit('uid not exist');
}

$new_id = pdo_fetchcolumn('SELECT notice_id FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid ORDER BY notice_id DESC LIMIT 1', array(':uid' => $_W['clerk']['id']));
$new_id = intval($new_id);
$notices = pdo_fetchall('SELECT id FROM ' . tablename('tiny_wmall_notice') . ' WHERE status = 1 AND type = :type AND id > :id', array(':type' => 'store', ':id' => $new_id));

if (!empty($notices)) {
	foreach ($notices as &$notice) {
		$insert = array('uid' => $_W['clerk']['id'], 'notice_id' => $notice['id'], 'is_new' => 1);
		pdo_insert('tiny_wmall_notice_read_log', $insert);
	}
}

$total = 0;
$total = pdo_fetchcolumn('SELECT COUNT(*) FROM' . tablename('tiny_wmall_notice_read_log') . ' WHERE uid = :uid AND is_new = 1', array(':uid' => $_W['clerk']['id']));
exit($total);

?>
