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
<div class="wrap ">
  <?php if (!defined('ZU_VERSION')) exit(); ?>
<?php  $getMenu = AdminbaseAction::getMenu(); if($getMenu) { ?>
<div class="nav">
  <ul class="cc">
    <?php
 foreach($getMenu as $r){ $app = $r['app']; $model = $r['model']; $action = $r['action']; ?>
    <li <?php echo $action==ACTION_NAME?'class="current"':""; ?>><a href="<?php echo U("".$app."/".$model."/".$action."",$r['data']);?>"><?php echo $r['name'];?></a></li>
    <?php
 } ?>
  </ul>
</div>
<?php } ?>
  <div class="h_a">温馨提示</div>
  <div class="prompt_text">
    <p>最好是有选择性的刷新，如果全部刷新，当信息量比较大的时候生成会比较久！</p>
  </div>
  <div class="h_a">刷新任务</div>
  <div class="table_full">
  <table width="100%" cellspacing="0">
      <form action="<?php echo U("Contents/Create_html/public_index");?>" method="post" name="myform">
        <tbody  height="200" class="nHover td-line">
          <tr>
            <th>从新生成首页
              <input type="button" name="dosubmit1" value=" 开始更新 " class="btn" onClick="myform.submit();"></th>
          </tr>
        </tbody>
      </form>
    </table>
  </div>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>