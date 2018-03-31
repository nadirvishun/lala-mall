<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('wxapp');
load()->classs('cloudapi');

$dos = array('front_download', 'domainset', 'code_uuid', 'code_gen', 'code_token', 'qrcode', 'checkscan', 'commitcode', 'preview', 'getpackage');
$do = in_array($do, $dos) ? $do : 'front_download';

$_W['page']['title'] = '小程序下载 - 小程序 - 管理';

$version_id = intval($_GPC['version_id']);
$wxapp_info = wxapp_fetch($_W['uniacid']);
if (!empty($version_id)) {
	$version_info = wxapp_version($version_id);
}
if ($do == 'domainset') {
	$appurl = $_W['siteroot'].'app/index.php';
	if($version_info) {
		$wxapp  = pdo_get('account_wxapp', array('uniacid'=>$version_info['uniacid']));
		if($wxapp && !empty($wxapp['appdomain'])) {
			$appurl = $wxapp['appdomain'];
		}
	}
	if($_W['ispost']) {
		$appurl = $_GPC['appurl'];
		if(! starts_with($appurl, 'https')) {
			itoast('域名必须以https开头');
			return;
		}
		if($version_info) {
			$update = pdo_update('account_wxapp', array('appdomain'=>$appurl), array('uniacid'=>$version_info['uniacid']));
			if($update) {
				itoast('更新小程序域名成功');
			}
			itoast('更新小程序域名失败');
		}
	}
	template('wxapp/version-front-download');
}

if ($do == 'front_download') {
	$appurl = $_W['siteroot'].'/app/index.php';
	$uptype = $_GPC['uptype'];
	$wxapp_versions_info = wxapp_version($version_id);
	if(!in_array($uptype, array('auto','normal'))) {
		$uptype = 'auto';
	}
	template('wxapp/version-front-download');
}

if($do == 'code_uuid') {
	$data = wxapp_code_generate($version_id);
	echo json_encode($data);
}

if($do == 'code_gen') {
	$code_uuid = $_GPC['code_uuid'];
	$data = wxapp_check_code_isgen($code_uuid);
	echo json_encode($data);
}

if ($do == 'code_token') {
	$tokendata = wxapp_code_token();
	echo json_encode($tokendata);
}

if ($do == 'qrcode') {
	$code_token = $_GPC['code_token'];
	header('Content-type: image/jpg');	echo wxapp_code_qrcode($code_token);
	exit;
}

if ($do == 'checkscan') {
	$code_token = $_GPC['code_token'];
	$last = $_GPC['last'];
	$data = wxapp_code_check_scan($code_token, $last);
	echo json_encode($data);
}

if($do == 'preview') {
	$code_token = $_GPC['code_token'];
	$code_uuid = $_GPC['code_uuid'];
	$data = wxapp_code_preview_qrcode($code_uuid, $code_token);
	echo json_encode($data);

}

if ($do == 'commitcode') {

	$user_version = $_GPC['user_version'];
	$user_desc = $_GPC['user_desc'];
	$code_token = $_GPC['code_token'];
	$code_uuid = $_GPC['code_uuid'];
	$data = wxapp_code_commit($code_uuid, $code_token, $user_version, $user_desc);
	echo json_encode($data);
}

if($do == 'getpackage') {
	if(empty($version_id)) {
		itoast('参数错误！', '', '');
	}
	$account_wxapp_info = wxapp_fetch($version_info['uniacid'], $version_id);
	if (empty($account_wxapp_info)) {
		itoast('版本不存在！', referer(), 'error');
	}
	$siteurl = $_W['siteroot'].'app/index.php';
	if(!empty($account_wxapp_info['appdomain'])) {
		$siteurl = $account_wxapp_info['appdomain'];
	}

	$request_cloud_data = array(
			'name' => $account_wxapp_info['name'],
			'modules' => $account_wxapp_info['version']['modules'],
			'siteInfo' => array(
					'name' => $account_wxapp_info['name'],
					'uniacid' => $account_wxapp_info['uniacid'],
					'acid' => $account_wxapp_info['acid'],
					'multiid' => $account_wxapp_info['version']['multiid'],
					'version' => $account_wxapp_info['version']['version'],
					'siteroot' => $siteurl,
					'design_method' => $account_wxapp_info['version']['design_method']
			),
			'tabBar' => json_decode($account_wxapp_info['version']['quickmenu'], true),
	);
	$result = wxapp_getpackage($request_cloud_data);

	if(is_error($result)) {
		itoast($result['message'], '', '');
	}else {
		header('content-type: application/zip');
		header('content-disposition: attachment; filename="' . $request_cloud_data['name'] . '.zip"');
		echo $result;
	}
	exit;
}
