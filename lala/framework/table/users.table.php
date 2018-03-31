<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */

defined('IN_IA') or exit('Access Denied');

class UsersTable extends We7Table {

	public function searchUsersList() {
		global $_W;
		$this->query->from('users', 'u')
				->select('u.*, p.avatar as avatar, p.mobile as mobile, p.uid as puid')
				->leftjoin('users_profile', 'p')
				->on(array('u.uid' => 'p.uid'))
				->orderby('u.uid', 'DESC');
		if (user_is_vice_founder()) {
			$this->query->where('u.owner_uid', $_W['uid']);
		}
		return $this->query->getall();
	}

	
	public function userOwnedAccount($uid) {
		$uniacid_list = $this->query->from('uni_account_users')->where('uid', $uid)->getall('uniacid');
		return array_keys($uniacid_list);
	}

	public function userOwnedAccountRole($uid, $uniacid = 0) {
		if (empty($uniacid)) {
			$role = $this->query->from('uni_account_users')->where('uid', $uid)->getall('role');
			return array_keys($role);
		} else {
			$role = $this->query->from('uni_account_users')->where(array('uid' => $uid, 'uniacid' => $uniacid))->get();
			return $role['role'];
		}
	}

	public function userPermission($uid, $uniacid) {
		return $this->query->from('users_permission')->where('uid', $uid)->where('uniacid', $uniacid)->getall('type');
	}

	public function searchWithStatus($status) {
		$this->query->where('u.status', $status);
		return $this;
	}

	public function searchWithType($type) {
		$this->query->where('u.type', $type);
		return $this;
	}

	public function searchWithFounder($founder_groupids) {
		$this->query->where('u.founder_groupid', $founder_groupids);
		return $this;
	}

	public function searchWithEndtime($day) {
		$this->query->where('u.endtime !=', 0)->where('u.endtime <', TIMESTAMP + 86400 * $day);
		return $this;
	}

	public function searchWithMobile() {
		$this->query->where('p.mobile !=', '');;
		return $this;
	}

	public function searchWithSendStatus() {
		$this->query->where('p.send_expire_status', 0);;
		return $this;
	}

	public function searchWithName($user_name) {
		$this->query->where('u.username LIKE', "%{$user_name}%");
		return $this;
	}

	public function searchWithOwnerUid($owner_uid) {
		$this->query->where('u.owner_uid', $owner_uid);
		return $this;
	}

	public function accountUsersNum($uid) {
		return $this->query->from('uni_account_users')->where('uid', $uid)->count();
	}

	public function usersGroup() {
		return $this->query->from('users_group')->getall('id');
	}

	public function usersGroupInfo($groupid) {
		return $this->query->from('users_group')->where('id', $groupid)->get();
	}

	public function usersInfo($uid) {
		return $this->query->from('users')->where('uid', $uid)->get();
	}

	public function usersFounderGroup() {
		return $this->query->from('users_founder_group')->getall('id');
	}

	public function userPermissionInfo($uid, $uniacid, $type = '') {
		$condition = array('uid' => $uid, 'uniacid' => $uniacid);
		if (!empty($type)) {
			$condition['type'] = $type;
		}
		return $this->query->from('users_permission')->where($condition)->get();
	}

	public function userModulesPermission($uid, $uniacid) {
		$condition = array(
			'uid'=> $uid,
			'uniacid' => $uniacid,
			'type !=' => array(PERMISSION_ACCOUNT, PERMISSION_WXAPP),
		);
		return $this->query->from('users_permission')->where($condition)->getall('type');
	}

	public function userFounderGroupInfo($uid) {
		return $this->query->from('users_founder_group')->where('id', $uid)->get();
	}

	public function userProfileMobile($mobile) {
		return $this->query->from('users_profile')->where('mobile', $mobile)->get();
	}

	public function userVerifyCode($receiver, $verifycode) {
		return $this->query->from('uni_verifycode')->where('receiver', $receiver)->where('verifycode', $verifycode)->where('uniacid', 0)->get();
	}
}