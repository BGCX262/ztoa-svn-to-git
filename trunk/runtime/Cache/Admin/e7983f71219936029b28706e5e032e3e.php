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
  <div class="h_a">常用菜单</div>
  <form class="J_ajaxForm" action="<?php echo U('Menu/public_changyong');?>" method="post">
    <div class="table_full J_check_wrap">
      <table width="100%">
        <col class="th" />
        <col width="400" />
        <col />
        <tr>
          <th><label>
              <input disabled=&quot;true&quot; checked id="J_role_custom" class="J_check_all" data-direction="y" data-checklist="J_check_custom" type="checkbox">
              <span>常用</span></label></th>
          <td><ul data-name="custom" class="three_list cc J_ul_check">
              <li>
                <label>
                  <input disabled checked data-yid="J_check_custom" class="J_check" type="checkbox" >
                  <span>常用菜单</span></label>
              </li>
            </ul></td>
          <td><div class="fun_tips"></div></td>
        </tr>
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i; $skey = $key;?>
          <tr>
            <th><label><input  id="J_role_<?php echo ($key); ?>" class="J_check_all" data-direction="y" data-checklist="J_check_<?php echo ($key); ?>" type="checkbox"><span><?php echo $name[ucwords($key)]?$name[ucwords($key)]:$key;?></span></label></th>
            <td>
                  <ul data-name="<?php echo ($key); ?>" class="three_list cc J_ul_check">
                  <?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$r): $mod = ($i % 2 );++$i;?><li><label><input  name="menu[]" data-yid="J_check_<?php echo ($skey); ?>" class="J_check" type="checkbox" value="<?php echo ($r["id"]); ?>" <?php if( in_array($r['id'],$panel) ): ?>checked<?php endif; ?>
><span><?php echo ($r["name"]); ?></span></label></li><?php endforeach; endif; else: echo "" ;endif; ?>
                  </ul>
              </td>
            <td><div class="fun_tips"></div></td>
          </tr><?php endforeach; endif; else: echo "" ;endif; ?>
      </table>
    </div>
    <div class="btn_wrap">
      <div class="btn_wrap_pd">
        <button class="J_ajax_submit_btn btn btn_submit" type="submit">提交</button>
      </div>
    </div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>