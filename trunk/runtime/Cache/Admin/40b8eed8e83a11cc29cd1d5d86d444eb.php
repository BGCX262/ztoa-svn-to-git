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
	<form method="post" class="J_ajaxForm" action="<?php echo U('Index/public_cache');?>">
	<div class="h_a">缓存更新</div>
	<div class="table_full">
		<table width="100%">
			<col class="th" />
			<col width="400" />
			<col />
			<tr>
				<th>更新站点数据缓存</th>
				<td>
					<a class="btn" href="<?php echo U('Index/public_cache',array('type'=>'site'));?>">提交</a>
				</td>
				<td><div class="fun_tips">修改过站点设置，或者栏目管理，模块安装等时可以进行缓存更新</div></td>
			</tr>
			<tr>
				<th>更新站点模板缓存</th>
				<td>
					<a class="btn" href="<?php echo U('Index/public_cache',array('type'=>'template'));?>">提交</a>
				</td>
				<td><div class="fun_tips">当修改模板时，模板没及时生效可以对模板缓存进行更新</div></td>
			</tr>
            <tr>
				<th>清除网站运行日志</th>
				<td>
					<a class="btn" href="<?php echo U('Index/public_cache',array('type'=>'logs'));?>">提交</a>
				</td>
				<td><div class="fun_tips">网站运行过程中会记录各种错误日志，以文件的方式保存</div></td>
			</tr>
		</table>
	</div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>