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
<div class="wrap">
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
  <div class="h_a">搜索</div>
  <div class="search_type cc mb10">
    <form action="<?php echo ($config_siteurl); ?>index.php" method="get">
      <input type="hidden" value="Admin" name="g">
      <input type="hidden" value="Censor" name="m">
      <input type="hidden" value="index" name="a">
      <input type="hidden" value="1" name="search">
      <div class="search_type cc mb10">
        <div class="mb10"> <span class="mr20"> 关键字：
          <input type="text" class="input length_2" name="keyword" style="width:200px;" value="<?php echo ($keyword); ?>" placeholder="请输入关键字...">
          <select class="select_2" name="type">
            <option value='0'>默认分类</option>
            <?php if(is_array($typedata)): $i = 0; $__LIST__ = $typedata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($tvo["id"]); ?>' 
              <?php if( $_GET['type'] == $tvo['id'] ): ?>selected<?php endif; ?>
              ><?php echo ($tvo["name"]); ?>
              </option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
          <button class="btn">搜索</button>
          </span> </div>
      </div>
    </form>
  </div>
  <form class="J_ajaxForm" action="<?php echo U('Admin/Censor/index');?>" method="post">
    <div class="table_list">
      <table width="100%">
        <colgroup>
        <col width="80">
        <col width="100">
        <col>
        <col width="200">
        <col width="200">
        </colgroup>
        <thead>
          <tr>
            <td>删除</td>
            <td>不良词语</td>
            <td>过滤动作</td>
            <td>词语分类</td>
            <td>操作者</td>
          </tr>
        </thead>
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
            <td ><input class="input" type="checkbox" name="delete[]" value="<?php echo ($vo["id"]); ?>" ></td>
            <td><input type="text" class="input" size="30" name="find[<?php echo ($vo["id"]); ?>]" value="<?php echo ($vo["find"]); ?>" ></td>
            <td><select name="replacement[<?php echo ($vo["id"]); ?>]" onChange="replaces(<?php echo ($vo["id"]); ?>,this.value);" >
                <option value="{BANNED}" <?php if( $vo['replacement'] == '{BANNED}' ): ?>selected<?php endif; ?> >禁止关键词</option>
                <option value="{MOD}" <?php if( $vo['replacement'] == '{MOD}' ): ?>selected<?php endif; ?>>审核关键词</option>
                <option value="{REPLACE}" <?php if( !in_array($vo['replacement'],array('{BANNED}','{MOD}')) ): ?>selected<?php endif; ?>>替换关键词</option>
              </select>
              <input class="input-text" type="text" size="30" name="replacontent[<?php echo ($vo["id"]); ?>]" id="replacontent_<?php echo ($vo["id"]); ?>" <?php if( in_array($vo['replacement'],array('{BANNED}','{MOD}')) ): ?>style="display:none" value="" disabled<?php else: ?>value="<?php echo ($vo['replacement']); ?>"<?php endif; ?>  >
              </td>
            <td>
            <select name='type[<?php echo ($vo["id"]); ?>]'>
                <option value='0' <?php if( $vo['type'] == '0' ): ?>selected<?php endif; ?>>默认分类</option>
                <?php if(is_array($typedata)): $i = 0; $__LIST__ = $typedata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($tvo["id"]); ?>' <?php if( $vo['type'] == $tvo['id'] ): ?>selected<?php endif; ?>><?php echo ($tvo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
              </select></td>
            <td><?php echo ($vo["admin"]); ?></td>
          </tr><?php endforeach; endif; else: echo "" ;endif; ?>
      </table>
      <div class="p10">
        <div class="pages"> <?php echo ($Page); ?> </div>
      </div>
    </div>
    <div class="">
      <div class="btn_wrap_pd">
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">提交</button>
      </div>
    </div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>