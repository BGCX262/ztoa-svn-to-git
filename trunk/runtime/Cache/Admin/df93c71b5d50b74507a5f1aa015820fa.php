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
  <div class="h_a">功能说明</div>
  <div class="prompt_text">
    <ul>
      <li>替换前的内容可以使用限定符 {x} 以限定相邻两字符间可忽略的文字，x 是忽略的字节数。如 "a{1}s{2}s"(不含引号) 可以过滤 "ass" 也可过滤 "axsxs" 和 "axsxxs" 等等。对于中文字符，使用 UTF-8 版本，每个中文字符相当于 3 个字节。</li>
      <li><font color="#FF0000">为不影响程序效率，请不要设置过多不需要的过滤内容。</font></li>
      <li>不良词语如果以"/"(不含引号)开头和结尾则表示格式为正则表达式，这时替换内容可用"(n)"引用正则中的子模式，如"/1\d{10}([^\d]+|$)/"替换为"手机(1)"。</li>
    </ul>
  </div>
  <form class="J_ajaxForm" action="<?php echo U('Admin/Censor/add');?>" method="post" id="myform">
    <div class="h_a">基本属性</div>
    <div class="table_full">
      <table width="100%" >
           <tr>
              <th width="200">不良词语：</th>
              <th><input type="text" name="name" value="" class="input length_6"></th>
            </tr>
          <tr>
            <th>过滤动作</th>
            <th><select name="replacement" onchange="replaces(this.value);" >
                <option value="{BANNED}" selected >禁止关键词</option>
                <option value="{MOD}" >审核关键词</option>
                <option value="{REPLACE}" >替换关键词</option>
              </select>
              <input class="input" type="text"  name="replacontent" id="replacontent" style="display:none" value="" disabled  ></th>
          </tr>
          <tr>
            <th>所属分类</th>
            <th><select name="type">
                <option value='0' selected>默认分类</option>
                <?php if(is_array($typedata)): $i = 0; $__LIST__ = $typedata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($tvo["id"]); ?>' ><?php echo ($tvo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
              </select>
              创建新分类：
              <input type="text" name="newtype" value="" class="input" size="30"></th>
          </tr>
      </table>
    </div>
    <div class="">
      <div class="btn_wrap_pd">             
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">提交</button>
      </div>
    </div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
<script type="text/javascript">
function replaces(value){
	if( value == '{REPLACE}' ){
		$("#replacontent").show().removeAttr("disabled");
	}else{
		$("#replacontent").hide().attr("disabled","disabled");
	}
}
</script>
</body>
</html>