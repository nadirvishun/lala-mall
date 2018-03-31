<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */

defined('IN_IA') or exit('Access Denied');

load()->model('mc');
load()->classs('wesession');
load()->classs('account');

$dos = array('chats', 'send', 'endchats');
$do = in_array($do , $dos) ? $do : 'chats';
permission_check_account_user('mc_fans');

if ($do == 'chats') {
	$_W['page']['title'] = '粉丝聊天';
	$openid = addslashes($_GPC['openid']);
	$fans_info = mc_fansinfo($openid);
	if (!empty($fans_info['uid'])) {
		$fans_info['member_info'] = mc_fetch($fans_info['uid']);
	}
	$chat_record = pdo_getslice('mc_chats_record', array('uniacid' => $_W['uniacid'], 'openid' => $openid), array('1', 20), $total, array(), '', 'createtime desc');
	$chat_record = mc_fans_chats_record_formate($chat_record);
}

if ($do == 'send') {
	$type = addslashes($_GPC['type']);
	$content = trim(htmlspecialchars_decode($_GPC['content']), '\"');
	$send['touser'] = trim($_GPC['openid']);
	$send['msgtype'] = $type;
	if ($type == 'text') {
		$send['text'] = array('content' => urlencode($content));
	} elseif ($type == 'image') {
		$send['image'] = array('media_id' => $content);
		$material = material_get($content);
		$content = $material['attachment'];
	} elseif ($type == 'voice') {
		$send['voice'] = array('media_id' => $content);
	} elseif($type == 'video') {
		$content = json_decode($content, true);
		$send['video'] = array(
			'media_id' => $content['mediaid'],
			'thumb_media_id' => '',
			'title' => urlencode($content['title']),
			'description' => ''
		);
	}  elseif($type == 'music') {
		$send['music'] = array(
			'musicurl' => tomedia($_GPC['musicurl']),
			'hqmusicurl' => tomedia($_GPC['hqmusicurl']),
			'title' => urlencode($_GPC['title']),
			'description' => urlencode($_GPC['description']),
			'thumb_media_id' => $_GPC['thumb_media_id'],
		);
	} elseif($type == 'news') {
		$content = json_decode($content, true);
		$send['msgtype'] =  'mpnews';
		$send['mpnews'] = array(
			'media_id' => $content['mediaid']
		);
	}
	$account_api = WeAccount::create($_W['acid']);
	$result = $account_api->sendCustomNotice($send);
	if (is_error($result)) {
		iajax(-1, $result['meaasge']);
	} else {
				$account = account_fetch($_W['acid']);
		$message['from'] = $_W['openid'] = $send['touser'];
		$message['to'] = $account['original'];
		if(!empty($message['to'])) {
			$sessionid = md5($message['from'] . $message['to'] . $_W['uniacid']);
			session_id($sessionid);
			WeSession::start($_W['uniacid'], $_W['openid'], 300);
			$processor = WeUtility::createModuleProcessor('chats');
			$processor->begin(300);
		}

		if($send['msgtype'] == 'mpnews') {
			$material = pdo_getcolumn('wechat_attachment', array('uniacid' => $_W['uniacid'], 'media_id' => $content['mediaid']), 'id');
			$content = $content['thumb'];
		}
				pdo_insert('mc_chats_record',array(
			'uniacid' => $_W['uniacid'],
			'acid' => $acid,
			'flag' => 1,
			'openid' => $send['touser'],
			'msgtype' => $send['msgtype'],
			'content' => iserializer($send[$send['msgtype']]),
			'createtime' => TIMESTAMP,
		));
		iajax(0, array('createtime' => date('Y-m-d H:i:s', time()), 'content' => $content, 'msgtype' => $send['msgtype']), '');
	}
}

if ($do == 'endchats') {
	$openid = trim($_GPC['openid']);
	if (empty($openid)) {
		iajax(1, '粉丝openid不合法', '');
	}
	$fans_info = mc_fansinfo($openid);
	$account = account_fetch($fans_info['acid']);
	$message['from'] = $_W['openid'] = $openid['openid'];
	$message['to'] = $account['original'];
	if(!empty($message['to'])) {
		$sessionid = md5($message['from'] . $message['to'] . $_W['uniacid']);
		session_id($sessionid);
		WeSession::start($_W['uniacid'], $_W['openid'], 300);
		$processor = WeUtility::createModuleProcessor('chats');
		$processor->end();
	}
	if (is_error($result)) {
		iajax(1, $result);
	}
}
template('mc/chats');