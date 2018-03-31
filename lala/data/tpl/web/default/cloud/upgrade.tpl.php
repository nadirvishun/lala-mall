<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<div class="upgrade-content">

	<div class="upgrade-heading we7-padding-vertical text-center">

		<img src="./resource/images/logo/logo-lg.png" alt="" class="we7-logo"/>

		<h2 class="upgrade-version">系统当前版本: <?php echo IMS_FAMILY;?><?php echo IMS_VERSION;?>（<?php echo IMS_RELEASE_DATE;?>）</h2>

	</div>

	<div class="upgrade-info we7-padding-bottom">

		<div class="panel we7-panel">

			<div class="panel-heading we7-padding">

				<span class="we7-padding-none color-gray">当前版本为最新版本，开始你的征程吧。</span>

			</div>

		</div>

		<div class="alert alert-danger">
			<i class="fa fa-exclamation-triangle"></i> 任何的升级更新，请注意提前备份数据！ 
		</div>

		<div class="text-center">

			<input name="submit" type="submit" value="立即检查新版本" class="btn btn-danger" />

			<input name="rollback" type="button" value="撤回更新" class="btn btn-default" data-toggle="modal" data-target="#rollback-panel" />

			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />

		</div>

	</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>

