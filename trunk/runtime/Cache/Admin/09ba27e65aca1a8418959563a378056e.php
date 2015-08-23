<?php if (!defined('THINK_PATH')) exit(); if (!defined('ZU_VERSION')) exit(); ?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title><?php echo ($Config["sitename"]); ?> - 提示信息</title>
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
  <div id="error_tips">
    <h2><?php echo ($msgTitle); ?></h2>
    <div class="error_cont">
      <ul>
        <li><?php echo ($error); ?></li>
      </ul>
      <div class="error_return"><em id="countdown_time"><span style="color:blue;font-weight:bold"><?php echo ($waitSecond/1000); ?></span></em>秒后自动跳转&nbsp;&nbsp;如不想等待，直接点：<a href="<?php echo ($jumpUrl); ?>" class="btn">返回</a></div>
    </div>
  </div>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
<script language="javascript">
setTimeout(function(){
	location.href = '<?php echo ($jumpUrl); ?>';
},<?php echo ($waitSecond); ?>);
          var max_sec=<?php echo ($waitSecond/1000); ?>;
        $(document).ready(function(){
          $("#countdown_time").html(max_sec);
          window.setInterval(SetRemainTime,1000);
        });    
        function SetRemainTime(){
            if(max_sec>1){
             $("#countdown_time").html(max_sec-1);  
             max_sec--;
             }
        }
</script>
</body>
</html>