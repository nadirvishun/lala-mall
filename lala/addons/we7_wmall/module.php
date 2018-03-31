<?php
//微擎应用 http://www.we7.cc   
defined('IN_IA') || exit('Access Denied');
include 'version.php';
include 'defines.php';
include 'model.php';
class We7_wmallModule extends WeModule
{
	public function welcomeDisplay()
	{
		header('location: ' . iurl('dashboard/index'));
		exit();
	}
}

?>
