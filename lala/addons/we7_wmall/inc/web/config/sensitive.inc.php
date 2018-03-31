<?php
/**
 * 外送系统
 * @author 微擎.源码
 * @QQ 2058430070
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '敏感词过滤';
if($_W['ispost']) {
	$sensitive = array(
		'sensitive_words' => array(),
		'replace_words' => array(),
		'group' => array(),
	);
	$sensitive_words = $_GPC['sensitive_words'];
	$replace_words = $_GPC['replace_words'];
	$group = array();
	if(!empty($sensitive_words)) {
		foreach($sensitive_words as $key => $val) {
			if(empty($val)) {
				unset($sensitive_words[$key]);
				unset($replace_words[$key]);
				continue;
			}
			if(empty($replace_words[$key])) {
				$replace_words[$key] = $val;
			}
			$group[] = array(
				'sensitive_words' => $val,
				'replace_words' => $replace_words[$key]
			);
		}
		$sensitive = array(
			'sensitive_words' => $sensitive_words,
			'replace_words' => $replace_words,
			'group' => $group
		);
	}
	set_system_config('sensitive', $sensitive);
	imessage(error(0, '设置敏感词成功'), referer(), 'ajax');
}

$sensitive = get_system_config('sensitive');
include itemplate('config/sensitive');