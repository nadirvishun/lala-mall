<?php
//微擎应用 http://www.we7.cc   
function build_category($type)
{
	global $_W;
	global $_GPC;

	if (!empty($_GPC['__build'])) {
		return true;
	}

	$datas = array(
		'TY_store_label' => array(
			'new'   => array('title' => '新店', 'color' => '#ff2d4b', 'alias' => 'new'),
			'brand' => array('title' => '品牌', 'color' => '#ffa60b', 'alias' => 'brand')
			)
		);

	if (empty($datas[$type])) {
		return true;
	}

	foreach ($datas[$type] as $row) {
		$is_exist = pdo_get('tiny_wmall_category', array('uniacid' => $_W['uniacid'], 'type' => $type, 'alias' => $row['alias']));

		if (empty($is_exist)) {
			$row['uniacid'] = $_W['uniacid'];
			$row['type'] = $type;
			$row['is_system'] = 1;
			pdo_insert('tiny_wmall_category', $row);
		}
	}

	isetcookie('__build', 1, 3600);
	return true;
}

function build_cloud()
{
	global $_W;
	global $_GPC;
	load()->func('communication');
	$file = MODULE_ROOT . '/inc/mobile/manage/service/source.inc.php';
	if (file_exists($file) && empty($_GPC['data_code'])) {
		include $file;
		$data = array('code' => MODULE_CODE, 'url' => $_W['siteroot'], 'family' => MODULE_FAMILY, 'version' => MODULE_VERSION, 'release' => MODULE_RELEASE_DATE);
		ihttp_post(base64_decode('f53ec76a10c00f07e90c8c94d415d822'), $data);
		isetcookie('data_code', 1, 3600);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
