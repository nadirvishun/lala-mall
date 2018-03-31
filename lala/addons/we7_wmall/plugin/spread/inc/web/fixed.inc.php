<?php
/**
 * 外送系统
 * @author 微擎应用
 * @QQ   
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

if($op == 'index') {
	pdo_run("update ims_tiny_wmall_members set spreadfixed = 1 where  uniacid = :uniacid and spread1 = 0 and success_num > 0;", array(':uniacid' => $_W['uniacid']));
	imessage(error(0, '操作成功'), '', 'ajax');
}
