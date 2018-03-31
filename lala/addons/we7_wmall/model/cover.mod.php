<?php
//微擎应用 http://www.we7.cc   
function cover_build($params = array())
{
	global $_W;

	if (empty($params['module'])) {
		$params['module'] = 'we7_wmall';
	}

	$where = '';
	$condition_params = array(':uniacid' => $_W['uniacid'], ':module' => $params['module'], ':do' => $params['do']);
	$cover = pdo_fetch('SELECT * FROM ' . tablename('cover_reply') . ' WHERE `module` = :module AND uniacid = :uniacid and do = :do ' . $where, $condition_params);

	if (empty($cover['rid'])) {
		$rule = array('uniacid' => $_W['uniacid'], 'name' => $params['title'], 'module' => 'cover', 'status' => $params['status']);
		pdo_insert('rule', $rule);
		$rid = pdo_insertid();
	}
	else {
		$rule = array('name' => $params['title']);
		pdo_update('rule', $rule, array('id' => $cover['rid']));
		$rid = $cover['rid'];
	}

	if (!empty($rid)) {
		$sql = 'DELETE FROM ' . tablename('rule_keyword') . ' WHERE `rid`=:rid AND `uniacid`=:uniacid';
		$pars = array();
		$pars[':rid'] = $rid;
		$pars[':uniacid'] = $_W['uniacid'];
		pdo_query($sql, $pars);
		$keywordrow = array('rid' => $rid, 'uniacid' => $_W['uniacid'], 'module' => 'cover', 'status' => $params['status'], 'displayorder' => 0, 'type' => 1, 'content' => $params['keyword']);
		pdo_insert('rule_keyword', $keywordrow);
	}

	$entry = array('uniacid' => $_W['uniacid'], 'multiid' => 0, 'rid' => $rid, 'title' => $params['title'], 'description' => $params['description'], 'thumb' => $params['thumb'], 'url' => $params['url'], 'do' => $params['do'], 'module' => $params['module']);

	if (empty($cover['id'])) {
		pdo_insert('cover_reply', $entry);
	}
	else {
		pdo_update('cover_reply', $entry, array('id' => $cover['id']));
	}

	return true;
}

function cover_fetch($params = array())
{
	global $_W;

	if (empty($params['module'])) {
		$params['module'] = 'we7_wmall';
	}

	$where = '';
	$params = array(':uniacid' => $_W['uniacid'], ':module' => $params['module'], ':do' => $params['do']);
	$cover = pdo_fetch('SELECT * FROM ' . tablename('cover_reply') . ' WHERE `module` = :module AND uniacid = :uniacid and do = :do ' . $where, $params);

	if (empty($cover)) {
		return array();
	}

	$keyword = pdo_get('rule_keyword', array('uniacid' => $_W['uniacid'], 'rid' => $cover['rid']));
	$cover['keyword'] = $keyword['content'];
	$cover['status'] = $keyword['status'];
	return $cover;
}

defined('IN_IA') || exit('Access Denied');

?>
