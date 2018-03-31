<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
$op = trim($_GPC['op']);

if ($op == 'list') {
	$key = trim($_GPC['key']);
	$data = pdo_fetchall('select * from ' . tablename('mc_mapping_fans') . ' where uniacid = :uniacid and (openid = :openid or nickname like :key) order by fanid desc limit 50', array(':uniacid' => $_W['uniacid'], ':key' => '%' . $key . '%', ':openid' => $key), 'fanid');

	if (!empty($data)) {
		foreach ($data as &$row) {
			if (is_base64($row['tag'])) {
				$row['tag'] = base64_decode($row['tag']);
			}

			if (is_serialized($row['tag'])) {
				$row['tag'] = @iunserializer($row['tag']);
			}

			if (!empty($row['tag']['headimgurl'])) {
				$row['tag']['avatar'] = tomedia($row['tag']['headimgurl']);
			}

			if ($row['tag']['sex'] == 1) {
				$row['tag']['sex'] = '男生';
			}
			else if ($row['tag']['sex'] == 2) {
				$row['tag']['sex'] = '女生';
			}
			else {
				$row['tag']['sex'] = '未知';
			}
		}

		$fans = array_values($data);
	}

	message(array('errno' => 0, 'message' => $fans, 'data' => $data), '', 'ajax');
}

?>
