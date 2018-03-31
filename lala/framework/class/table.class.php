<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
 
defined('IN_IA') or exit('Access Denied');


abstract class We7Table {
	protected $query;
	
	public function __construct() {
				$this->query = load()->object('Query');
		$this->query->from('');
	}
	
	
	public function searchWithPage($pageindex, $pagesize) {
		if (!empty($pageindex) && !empty($pagesize)) {
			$this->query->page($pageindex, $pagesize);
		}
		
		return $this;
	}
	
	
	public function getLastQueryTotal() {
		return $this->query->getLastQueryTotal();
	}
}