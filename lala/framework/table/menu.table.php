<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */

defined('IN_IA') or exit('Access Denied');

class MenuTable extends We7Table {

	private $account_menu_table = 'uni_account_menus';

	
	public function searchAccountMenuList($type = '') {
		global $_W;
		$this->query->from($this->account_menu_table)->where('uniacid', $_W['uniacid']);
		if (!empty($type)) {
			$this->query->where('type', $type);
		}
		$result = $this->query->getall('id');
		return $result;
	}
	public function accountMenuInfo($condition = array()) {
		global $_W;
		$fields = array('id', 'menuid', 'type', 'status');

		$this->query->from($this->account_menu_table)->where('uniacid', $_W['uniacid']);
		if (!empty($condition)) {
			foreach ($condition as $key => $val) {
				if (in_array($key, $fields)) {
					$this->query->where($key, $val);
				}
			}
		}
		$result = $this->query->get();
		return $result;
	}
	public function accountDefaultMenuInfo() {
		global $_W;
		$this->query->from($this->account_menu_table)->where('uniacid', $_W['uniacid'])->where('type', MENU_CURRENTSELF)->where('status', STATUS_ON);
		$result = $this->query->get();
		return $result;
	}
}