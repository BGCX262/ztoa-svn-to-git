<?php if (!defined('THINK_PATH')) exit(); if (!defined('ZU_VERSION')) exit(); ?>
<?php if (!defined('ZU_VERSION')) exit(); ?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>系统后台 - <?php echo ($Config["sitename"]); ?> - by ShuipFCMS</title>
<?php if (!defined('ZU_VERSION')) exit(); ?><link href="<?php echo ($config_siteurl); ?>statics/css/admin_style.css" rel="stylesheet" />
<link href="<?php echo ($config_siteurl); ?>statics/js/artDialog/skins/default.css" rel="stylesheet" />
<?php if (!defined('ZU_VERSION')) exit(); ?>
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "<?php echo ($config_siteurl); ?>",
    JS_ROOT: "statics/js/",
    TOKEN: "<?php echo ($__token__); ?>"
};
</script>
<script src="<?php echo ($config_siteurl); ?>statics/js/wind.js"></script>
<script src="<?php echo ($config_siteurl); ?>statics/js/jquery.js"></script>
</head>
<body>
<div class="wrap">
  <div id="home_toptip"></div>
  <h2 class="h_a">系统信息</h2>
  <div class="home_info">
    <ul>
      <?php if(is_array($server_info)): $i = 0; $__LIST__ = $server_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li> <em><?php echo ($key); ?></em> <span><?php echo ($vo); ?></span> </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </div>
  <h2 class="h_a">开发团队</h2>
  <div class="home_info" id="home_devteam">
    <ul>
      <li> <em>版权所有</em> <span>www.85zu.com</span> </li>
      <li> <em>负责人</em> <span>兰斌斌</span> </li>
      <li> <em>联系邮箱</em> <span>lanbinbin@85zu.com</span> </li>
    </ul>
  </div>
</div>
<!--升级提示-->
<div id="J_system_update" style="display:none" class="system_update"> 您正在使用旧版本，为了获得更好的体验，请升级至最新版本。<a href="">立即升级</a> </div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script> 
<script>
$("#btn_submit").click(function(){
	$("#tips_success").fadeTo(500,1);
});
//获取升级信息通知
$.ajax({
    url: "<?php echo U('Public/public_notice');?>",
    dataType: "json",
    success: function (data) {
    	var r = data.data;
    	if (r.notice) {
    		$('#J_system_update').show();
    		$('#J_system_update').html(r.notice + "<a href='" + r.url +"'>立即升级</a>");
    	}
    },
    error: function () {
    }
});
</script>
</body>
</html>