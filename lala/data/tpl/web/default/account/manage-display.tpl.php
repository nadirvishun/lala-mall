<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">公众号管理</div>
<ul class="we7-page-tab">
	<li class="active"><a href="<?php  echo url('account/manage');?>">公众号列表</a></li>
	<?php  if($_W['role'] == ACCOUNT_MANAGE_NAME_OWNER || $_W['role'] == ACCOUNT_MANAGE_NAME_FOUNDER) { ?>
	<li><a href="<?php  echo url('account/recycle');?>">公众号回收站</a></li>
	<?php  } ?>
</ul>
<div class="clearfix we7-margin-bottom">
	<?php  if(!$_W['isfounder'] && !empty($account_info['uniacid_limit']) || user_is_vice_founder()) { ?>
		<div class="alert alert-warning">
			温馨提示：
			<i class="fa fa-info-circle"></i>
			Hi，<span class="text-strong"><?php  echo $_W['username'];?></span>，您所在的会员组： <span class="text-strong"><?php  echo $account_info['group_name'];?></span>，<?php  if(!user_is_vice_founder() && !empty($account_info['vice_group_name'])) { ?> <span class="text-strong"><?php  echo $account_info['vice_group_name'];?>，</span><?php  } ?>
			账号有效期限：<span class="text-strong"><?php  echo date('Y-m-d', $_W['user']['starttime'])?> ~~ <?php  if(empty($_W['user']['endtime'])) { ?>无限制<?php  } else { ?><?php  echo date('Y-m-d', $_W['user']['endtime'])?><?php  } ?></span>，
			可创建 <span class="text-strong"><?php  echo $account_info['maxaccount'];?> </span>个公众号，已创建<span class="text-strong"> <?php  echo $account_info['uniacid_num'];?> </span>个，还可创建 <span class="text-strong"><?php  echo $account_info['uniacid_limit'];?> </span>个公众号。
		</div>
	<?php  } ?>
	<form action="" class="form-inline  pull-left" method="get">
		<input type="hidden" name="c" value="account">
		<input type="hidden" name="a" value="manage">
		<div class="input-group form-group" style="width: 400px;">
			<input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" class="form-control" placeholder="搜索关键字"/>
			<span class="input-group-btn"><button class="btn btn-default"><i class="fa fa-search"></i></button></span>
		</div>
	</form>
	<?php  if(!empty($account_info['uniacid_limit']) || $_W['isfounder'] && !user_is_vice_founder()) { ?>
	<div class="pull-right">
		<a href="<?php  echo url('account/post-step');?>" class="btn btn-primary we7-padding-horizontal">添加公众号</a>
	</div>
	<?php  } ?>
</div>
<table class="table we7-table table-hover vertical-middle table-manage" id="js-system-account-display" ng-controller="SystemAccountDisplay" ng-cloak>
	<col width="120px" />
	<col/>
	<col width="200px"/>
	<col width="100px"/>
	<col width="260px" />
	<tr>
		<th colspan="5" class="text-left filter">
			<div class="dropdown dropdown-toggle we7-dropdown">
				<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					时间排序
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" aria-labelledby="dLabel">
					<li><a href="<?php  echo filter_url('order:asc');?>" class="active">创建时间正序</a></li>
					<li><a href="<?php  echo filter_url('order:desc');?>">创建时间倒序</a></li>
				</ul>
			</div>
			<div class="dropdown dropdown-toggle we7-dropdown">
				<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					公众号筛选
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" aria-labelledby="dLabel">
					<li><a href="<?php  echo filter_url('type:all');?>" class="active">全部公众号</a></li>
					<li><a href="<?php  echo filter_url('type:expire');?>" class="active">公众号已到期</a></li>
					<li><a href="<?php  echo filter_url('type:noconnect');?>" class="active">未接入公众号</a></li>
				</ul>
			</div>
		</th>
	</tr>
	<tr>
		<th colspan="2" class="text-left">帐号</th>
		<th>有效期</th>
		<th>短信数(条)</th>
		<th class="text-right">操作</th>
	</tr>
	<tr class="color-gray" ng-repeat="list in lists">
		<td class="text-left td-link">
			<?php  if($role_type) { ?>
			<a ng-href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}">
			<?php  } else { ?>
			<a href="javascript:;">
			<?php  } ?>
				<img ng-src="{{list.logo}}" class="img-responsive icon">
			</a>
		</td>
		<td class="text-left">
			<p class="color-dark" ng-bind="list.name"></p>
			<span class="color-gray" ng-if="list.level == 1">类型：普通订阅号</span>
			<span class="color-gray" ng-if="list.level == 2">类型：普通服务号</span>
			<span class="color-gray" ng-if="list.level == 3">类型：认证订阅号</span>
			<span class="color-gray" ng-if="list.level == 4" title="认证服务号/认证媒体/政府订阅号">类型：认证服务号</span>
			<span class="color-red" ng-if="list.isconnect == 0" data-toggle="tooltip" data-placement="right" title="公众号接入状态显示“未接入”解决方案：进入微信公众平台，依次选择: 开发者中心 -> 修改配置，然后将对应公众号在平台的url和token复制到微信公众平台对应的选项，公众平台会自动进行检测"><i class="wi wi-error-sign"></i>未接入</span>
			<span class="color-green" ng-if="list.isconnect == 1"><i class="wi wi-right-sign"></i>已接入</span>
		</td>
		<td>
			<p ng-bind="list.setmeal.timelimit"></p>
		</td>
		<td><p ng-bind="list.sms"></p></td>
		<td class="vertical-middle table-manage-td">
			<div class="link-group">
				<a ng-href="{{links.switch}}uniacid={{list.uniacid}}">进入公众号</a>
				<?php  if($role_type) { ?>
				<a ng-href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}" ng-show="list.role == 'manager' || list.role == 'owner' || list.role == 'founder'|| list.role == 'vice_founder'">管理设置</a>
				<?php  } ?>
			</div>
			<?php  if($role_type) { ?>
			<div class="manage-option text-right">
				<a href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}">基础信息</a>
				<a href="{{links.post}}&do=sms&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}">短信信息</a>
				<a href="{{links.postUser}}&do=edit&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}">使用者管理</a>
				<a href="{{links.post}}&do=modules_tpl&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}">可用应用模板/模块</a>
				<?php  if($_W['role'] != ACCOUNT_MANAGE_NAME_MANAGER) { ?>
				<a ng-href="{{links.del}}&acid={{list.acid}}&uniacid={{list.uniacid}}" ng-show="list.role == 'owner' || list.role == 'founder'" onclick="if(!confirm('确认放入回收站吗？')) return false;" class="del">停用</a>
				<?php  } ?>
			</div>
			<?php  } ?>
		</td>
	</tr>
</table>
<div class="text-right">
	<?php  echo $pager;?>
</div>
<script>
	$(function(){
		$('[data-toggle="tooltip"]').tooltip();
	});
	switch_url = "<?php  echo url('account/display/switch')?>";
	angular.module('accountApp').value('config', {
		lists: <?php echo !empty($list) ? json_encode($list) : 'null'?>,
		links: {
			switch: switch_url,
			post: "<?php  echo url('account/post')?>",
			postUser: "<?php  echo url('account/post-user')?>",
			del: "<?php  echo url('account/manage/delete')?>",
		}
	});
	angular.bootstrap($('#js-system-account-display'), ['accountApp']);
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>