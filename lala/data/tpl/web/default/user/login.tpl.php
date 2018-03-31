<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<div class="system-login" <?php  if(!empty($_W['setting']['copyright']['background_img'])) { ?> style="background-image:url('<?php  echo tomedia($_W['setting']['copyright']['background_img']);?>')" <?php  } else { ?> style="background-image: url('./resource/images/bg-login.png');" <?php  } ?>>

	<div class="head">
		<a href="/" class="logo-version">
			<img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo tomedia($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/images/logo/logo.png<?php  } ?>" class="logo">
			<span class="version hidden"><?php echo IMS_VERSION;?></span>
		</a>
		<?php  if(!empty($_W['setting']['copyright']['showhomepage'])) { ?>
		<a href="<?php  echo url('account/welcome')?>" class="pull-right">首页</a>
		<?php  } ?>
	</div>
	<div class="login-panel">
		<div class="title">账号密码登录</div>
		<form action="" method="post" role="form" id="form1" onsubmit="return formcheck();" class="we7-form">
			<div class="input-group-vertical">
				<input name="username" type="text" class="form-control " placeholder="请输入用户名登录">
				<input name="password" type="password" class="form-control password" placeholder="请输入登录密码">
				<?php  if(!empty($_W['setting']['copyright']['verifycode'])) { ?>
				<div class="input-group">
					<input name="verify" type="text" class="form-control" placeholder="请输入验证码">
					<a href="javascript:;" id="toggle" class="input-group-btn imgverify"><img id="imgverify" src="<?php  echo url('utility/code')?>" title="点击图片更换验证码" /></a>
				</div>
				<?php  } ?>
			</div>
			<div class="form-inline" style="margin-bottom: 15px;">
				<div class="pull-right">
					<a href="<?php  echo url('user/find-password');?>" target="_blank" class="color-default">忘记密码？</a>
				</div>
				<div class="checkbox">
					<input type="checkbox" value="true" id="rember" name="rember">
					<label for="rember">记住用户名</label>
				</div>
			</div>
			<div class="login-submit text-center">
				<input type="submit" id="submit" name="submit" value="登录" class="btn btn-primary btn-block" />
				<div class="text-right">
					<?php  if(!$_W['siteclose'] && $setting['register']['open']) { ?>
						<a href="<?php  echo url('user/register');?>" class="color-default">立即注册</a>
					<?php  } ?>
				</div>
				<input name="token" value="<?php  echo $_W['token'];?>" type="hidden" />
			</div>
			<?php  if(!empty($login_urls['qq']) || !empty($login_urls['wechat'])) { ?>
			<div class="text-center">
				<span class="color-gray">使用第三方账号登录</span>
				<div class="form-control-static">
					<?php  if(!empty($setting['thirdlogin']['qq']['authstate'])) { ?><a href="<?php  echo $login_urls['qq'];?>"><img src="./resource/images/qqlogin.png" width="35px"></a>&nbsp;&nbsp;<?php  } ?>
					<?php  if(!empty($setting['thirdlogin']['wechat']['authstate'])) { ?><a href="<?php  echo $login_urls['wechat'];?>"><img src="./resource/images/wxlogin.png" width="35px"></a><?php  } ?>
				</div>
			</div>
			<?php  } ?>
		</form>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-base', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-base', TEMPLATE_INCLUDEPATH));?>
<script>
function formcheck() {
	if($('#remember:checked').length == 1) {
		cookie.set('remember-username', $(':text[name="username"]').val());
	} else {
		cookie.del('remember-username');
	}
	return true;
}
var h = document.documentElement.clientHeight;
$(".system-login").css('height',h);
$('#toggle').click(function() {
	$('#imgverify').prop('src', '<?php  echo url('utility/code')?>r='+Math.round(new Date().getTime()));
	return false;
});
<?php  if(!empty($_W['setting']['copyright']['verifycode'])) { ?>
	$('#form1').submit(function() {
		var verify = $(':text[name="verify"]').val();
		if (verify == '') {
			alert('请填写验证码');
			return false;
		}
	});
<?php  } ?>
</script>
