<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
load()->func('communication');
load()->model('attachment');

$dos = array('display', 'save', 'test_setting');
$do = in_array($do, $dos) ? $do : 'display';
$_W['page']['title'] = '远程附件 - 公众号选项';

$remote = $_W['setting']['remote'];
if ($do == 'save'){
	$remote_data = array(
		'type' => $_GPC['type'],
		'qiniu' => array(
			'accesskey' => $_GPC['qiniu']['accesskey'],
			'secretkey' => strexists($_GPC['qiniu']['secretkey'], '*') ? $remote[$_W['uniacid']]['qiniu']['secretkey'] : trim($_GPC['qiniu']['secretkey']),
			'bucket' => $_GPC['qiniu']['bucket'],
			'url' => $_GPC['qiniu']['url'],
		)
	);
	if ($remote_data['type'] == ATTACH_QINIU) {
		if (empty($remote_data['qiniu']['accesskey'])) {
			itoast ('请填写Accesskey', referer (), 'info');
		}
		if (empty($remote_data['qiniu']['secretkey'])) {
			itoast ('secretkey', referer (), 'info');
		}
		if (empty($remote_data['qiniu']['bucket'])) {
			itoast ('请填写bucket', referer (), 'info');
		}
		if (empty($remote_data['qiniu']['url'])) {
			itoast ('请填写url', referer (), 'info');
		} else {
			$remote_data['qiniu']['url'] = strexists ($remote_data['qiniu']['url'], 'http') ? trim ($remote_data['qiniu']['url'], '/') : 'http://' . trim ($remote_data['qiniu']['url'], '/');
		}
		$auth = attachment_qiniu_auth ($remote_data['qiniu']['accesskey'], $remote_data['qiniu']['secretkey'], $remote_data['qiniu']['bucket']);
		if (is_error ($auth)) {
			$message = $auth['message']['error'] == 'bad token' ? 'Accesskey或Secretkey填写错误， 请检查后重新提交' : 'bucket填写错误或是bucket所对应的存储区域选择错误，请检查后重新提交';
			itoast ($message, referer (), 'info');
		}
	}
	$remote[$_W['uniacid']] = $remote_data;
	setting_save($remote, 'remote');
	itoast('保存成功', '', 'success');
}

if ($do == 'test_setting') {
	$type = $_GPC['type'];
	if ($type == ATTACH_QINIU) {
		$_GPC['secretkey'] = strexists($_GPC['secretkey'], '*') ? $remote[$_W['uniacid']]['qiniu']['secretkey'] : $_GPC['secretkey'];
		$auth= attachment_qiniu_auth(trim($_GPC['accesskey']), trim($_GPC['secretkey']), trim($_GPC['bucket']));
		if (is_error($auth)) {
			iajax(-1, '配置失败，请检查配置。注：请检查存储区域是否选择的是和bucket对应<br/>的区域', '');
		}
		$url = $_GPC['url'];
		$url = strexists($url, 'http') ? trim($url, '/') : 'http://'.trim($url, '/');
		$filename = 'MicroEngine.ico';
		$response = ihttp_request($url. '/'.$filename, array(), array('CURLOPT_REFERER' => $_SERVER['SERVER_NAME']));
		if (is_error($response)) {
			iajax(-1, '配置失败，七牛访问url错误');
		}
		if (intval($response['code']) != 200) {
			iajax(-1, '配置失败，七牛访问url错误,请保证bucket为公共读取的');
		}
		$image = getimagesizefromstring($response['content']);
		if (!empty($image) && strexists($image['mime'], 'image')) {
			iajax(0,'配置成功');
		} else {
			iajax(-1, '配置失败，七牛访问url错误');
		}
	}
}

template('profile/remote');