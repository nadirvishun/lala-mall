<?php
defined('IN_IA') || exit('Access Denied');
global $_GPC;
global $_W;
$op = ((trim($_GPC['op']) ? trim($_GPC['op']) : 'index'));
$_W['page']['title'] = '分享超级红包';
if (!(is_weixin())) 
{
	imessage('请在微信中访问该链接', '', 'info');
}
if (!($_W['ispost'])) 
{
	$order_id = intval($_GPC['order_id']);
	$grant = pdo_get('tiny_wmall_superredpacket_grant', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if (empty($grant)) 
	{
		imessage('分享记录不存在', referer(), 'error');
	}
	$activity = pdo_get('tiny_wmall_superredpacket', array('uniacid' => $_W['uniacid'], 'id' => $grant['activity_id'], 'type' => 'share'));
	if (empty($activity)) 
	{
		imessage('分享红包活动不存在', referer(), 'error');
	}
	$_W['page']['title'] = $activity['name'];
	$activity['data'] = json_decode(base64_decode($activity['data']), true);
	$_share = array('title' => $activity['data']['share']['title'], 'desc' => $activity['data']['share']['desc'], 'imgUrl' => tomedia($activity['data']['share']['imgUrl']), 'link' => imurl('superRedpacket/share/index', array('order_id' => $order_id), true));
}
if ($op == 'index') 
{
	$order_id = intval($_GPC['order_id']);
	if ($_W['ispost']) 
	{
		$mobile = ((trim($_GPC['mobile']) ? trim($_GPC['mobile']) : imessage(error(-1, '请输入手机号'), '', 'ajax')));
		$openid = ((trim($_GPC['fans']['openid']) ? trim($_GPC['fans']['openid']) : imessage(error(-1, '微信信息错误'), '', 'ajax')));
		$is_exist = pdo_fetch('select id from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and openid != :openid and mobile = :mobile', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':mobile' => $mobile));
		if (!(empty($is_exist))) 
		{
			imessage(error(-1, '该手机号已绑定其他用户的微信'), referer(), 'ajax');
		}
		$params = array('openid' => $openid, 'nickname' => $_GPC['fans']['nickname'], 'headimgurl' => $_GPC['fans']['headimgurl'], 'sex' => $_GPC['fans']['sex'], 'mobile' => $mobile);
		mload()->model('member');
		$new_member = member_register($params);
		if (is_error($new_member)) 
		{
			imessage($new_member['message'], '', 'error');
			imessage(error(-1, $new_member['message']), referer(), 'ajax');
		}
		imessage(error(0, ''), '', 'ajax');
	}
	$fansInfo = mc_oauth_userinfo();
	if (is_error($fansInfo)) 
	{
		imessage('微信授权失败,请重新访问该链接', 'refresh', 'info');
	}
	$is_exist = pdo_get('tiny_wmall_members', array('uniacid' => $_W['uniacid'], 'openid' => $fansInfo['openid']));
	if (!(empty($is_exist))) 
	{
		header('location:' . imurl('superRedpacket/share/grant', array('order_id' => $order_id)));
		exit();
	}
}
if ($op == 'grant') 
{
	icheckauth();
	if ($activity['status'] != 1) 
	{
		include itemplate('share');
		exit();
	}
	$is_get = 0;
	$get_status = 0;
	$is_exist = pdo_get('tiny_wmall_activity_redpacket_record', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'super_share_id' => $grant['id'], 'channel' => 'superRedpacket', 'type' => 'share'), array('id'));
	if (empty($is_exist) && (0 < $grant['packet_dosage'])) 
	{
		$result = superRedpacket_share_grant($order_id);
		if (is_error($result)) 
		{
			imessage($result['message'], referer(), 'error');
		}
		$get_status = 1;
	}
	$redpackets = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and super_share_id = :super_share_id and uid = :uid and type = :type and channel = :channel', array(':uniacid' => $_W['uniacid'], ':super_share_id' => $grant['id'], ':uid' => $_W['member']['uid'], ':type' => 'share', ':channel' => 'superRedpacket'));
	if (!(empty($redpackets))) 
	{
		$is_get = 1;
		foreach ($redpackets as &$val ) 
		{
			$val['condition_cn'] = date('Y-m-d', $val['starttime']) . '~' . date('Y-m-d', $val['endtime']) . '有效';
			$val['category_cn'] = tocategory($val['category_limit']);
			if (!(empty($val['category_cn']))) 
			{
				$val['category_cn'] = '仅限' . tocategory($val['category_limit']) . '分类使用';
			}
			$val['times_cn'] = totime($val['times_limit']);
			if (!(empty($val['times_cn']))) 
			{
				$val['times_cn'] = '仅限' . $val['times_cn'] . '时段使用';
			}
		}
	}
	$rankings = pdo_fetchall('select uid,granttime,sum(discount) as total_discount from ' . tablename('tiny_wmall_activity_redpacket_record') . ' where uniacid = :uniacid and super_share_id = :super_share_id and channel = :channel and type = :type group by uid order by total_discount desc', array(':uniacid' => $_W['uniacid'], ':super_share_id' => $grant['id'], ':channel' => 'superRedpacket', ':type' => 'share'), 'uid');
	if (!(empty($rankings))) 
	{
		$uids = array_keys($rankings);
		$uids = implode(',', $uids);
		$members = pdo_fetchall('select uid,avatar,nickname from ' . tablename('tiny_wmall_members') . ' where uniacid = :uniacid and uid in(' . $uids . ')', array(':uniacid' => $_W['uniacid']), 'uid');
	}
}
include itemplate('share');
?>