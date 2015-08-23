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
  <div class="nav">
    <ul class="cc">
      <li class="current"><a href="<?php echo U('Style/index');?>">模板管理</a></li>
      <li><a href="<?php echo U("Template/Style/add",array("dir"=>urlencode(str_replace('/','-',$dir)) ));?>">在此目录下添加模板</a></li>
    </ul>
  </div>
  <form action="<?php echo U("Style/updatefilename");?>" method="post" class="J_ajaxForm">
  <div class="table_list">
  <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td align="left" width="30%">目录列表</td>
            <td align="left" width="55%" >说明</td>
            <td align="left"  width="15%">操作</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td align="left" colspan="3">当前目录：<?php echo ($local); ?></td>
          </tr>
          <?php if($dir != '' && $dir != '.' ): ?><tr>
            <td align="left" colspan="3"><a href="<?php echo U("Template/Style/index",array("dir"=>urlencode( str_replace(basename($dir).'-','',str_replace('/','-',$dir)) ) ) );?>"><img src="<?php echo ($config_siteurl); ?>statics/images/folder-closed.gif" />上一层目录</a></td>
          </tr><?php endif; ?>
          <?php if(is_array($tplist)): $i = 0; $__LIST__ = $tplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
            <td align="left">
            <?php if( '.'.fileext(basename($vo)) == C('TMPL_TEMPLATE_SUFFIX')): ?><img src="<?php echo ($tplextlist[$vo]); ?>" />
            <a href="<?php echo U("Template/Style/edit_file",array("dir"=>urlencode(str_replace('/','-',$dir)),"file"=>basename($vo)));?>"><b><?php echo (basename($vo)); ?></b></a></td>
            <td align="left"><input type="text" class="input length_6 " name="file_explan[<?php echo ($encode_local); ?>][<?php echo (basename($vo)); ?>]" value="<?php echo (isset($file_explan[$encode_local][basename($vo)]) ? $file_explan[$encode_local][basename($vo)] : "")?>"></td>
            <td> <a href="<?php echo U("Template/Style/edit_file",array("dir"=>urlencode(str_replace('/','-',$dir)) ,"file"=>basename($vo)));?>">[修改]</a> | <a href="javascript:confirmurl('<?php echo U("Template/Style/delete",array("dir"=>urlencode(str_replace('/','-',$dir)) ,"file"=>basename($vo)));?>','确认要删除吗？')">[删除]</a></td>
            <?php elseif(substr($tplextlist[$vo],-strlen($dirico))!=$dirico): ?>
            <img src="<?php echo ($tplextlist[$vo]); ?>" />
            <b><?php echo (basename($vo)); ?></b></td>
            <td align="left"><input type="text" class="input length_6 " name="file_explan[<?php echo ($encode_local); ?>][<?php echo (basename($vo)); ?>]" value="<?php echo (isset($file_explan[$encode_local][basename($vo)]) ? $file_explan[$encode_local][basename($vo)] : "")?>"></td>
            <td></td>
            <?php else: ?>
            <img src="<?php echo ($tplextlist[$vo]); ?>" />
            <a href="<?php echo U("Template/Style/index",array("dir"=>urlencode(str_replace('/','-',$dir).basename($vo).'-') ));?>"><b><?php echo (basename($vo)); ?></b></a></td>
            <td align="left"><input type="text" class="input length_6 " name="file_explan[<?php echo ($encode_local); ?>][<?php echo (basename($vo)); ?>]" value="<?php echo (isset($file_explan[$encode_local][basename($vo)]) ? $file_explan[$encode_local][basename($vo)] : "")?>"></td>
            <td></td><?php endif; ?>
          </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
      </table>
  </div>
   <div class="">
      <div class="btn_wrap_pd">             
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">更新</button>
      </div>
    </div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>