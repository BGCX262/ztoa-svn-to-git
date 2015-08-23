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
<body class="J_scroll_fixed">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="180">
    <iframe name="left" id="iframe_categorys" src="<?php echo U('Contents/Content/public_categorys');?>" style="height: 100%; width: 180px;"  frameborder="0" scrolling="auto"></iframe></td>
    <td width="3" bgcolor="#CCCCCC">
    </td>
    <td>
    <iframe name="right" id="iframe_categorys_list" src="<?php echo U('Admin/Main/index');?>"   style="height: 100%; width:100%;border:none;"   frameborder="0" scrolling="auto"></iframe></td>
  </tr>
</table>
<script type="text/javascript">
var B_frame_height = parent.$("#B_frame").height()-8;
$(window).on('resize', function () {
    setTimeout(function () {
		B_frame_height = parent.$("#B_frame").height()-8;
        frameheight();
    }, 100);
});
function frameheight(){
	$("#iframe_categorys").height(B_frame_height);
	$("#iframe_categorys_list").height(B_frame_height);
}
(function (){
	frameheight();
})();
</script>
</body>
</html>