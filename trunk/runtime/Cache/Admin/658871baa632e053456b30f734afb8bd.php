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
<div class="wrap J_check_wrap">
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
<div class="h_a">说明</div>
<div class="prompt_text">
  <p>删除分类会同时删除该分类下的信息！</p>
</div>
<form class="J_ajaxForm" action="<?php echo U('Admin/Censor/classify');?>" method="post">
  <div class="table_list">
    <table width="100%" cellspacing="0">
      <thead>
        <tr>
          <td width="80" align="left">删除</td>
          <td  align="left">分类名称</td>
        </tr>
      </thead>
      <tbody>
        <?php if(is_array($typedata)): $i = 0; $__LIST__ = $typedata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
            <td><input class="input-text" type="checkbox" name="delete[]" value="<?php echo ($vo["id"]); ?>" ></td>
            <td><input type="text" class="input" size="30" name="name[<?php echo ($vo["id"]); ?>]" value="<?php echo ($vo["name"]); ?>" ></td>
          </tr><?php endforeach; endif; else: echo "" ;endif; ?>
      </tbody>
    </table>
  </div>
  <div class="">
    <div class="btn_wrap_pd">
      <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">提交</button>
    </div>
  </div>
</form>
<div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>