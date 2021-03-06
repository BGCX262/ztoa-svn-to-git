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
  <div class="table_list">
  <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td >模块名称</td>
            <td width="90">模块目录</td>
            <td width="180">版本号</td>
            <td width="120">安装日期</td>
            <td width="120">更新日期</td>
            <td width="120">操作</td>
          </tr>
        </thead>
        <tbody>
<?php  if (is_array($data)){ foreach ($data as $d){ if (array_key_exists($d, $modules)) { ?>   
	<tr>
	<td width="220"><?php echo $modules[$d]['name']?></td>
	<td width="220"><?php echo $d?></td>
	<td><?php echo $modules[$d]['version']?></td>
	<td><?php echo $modules[$d]['installdate']?></td>
	<td><?php echo $modules[$d]['updatedate']?></td>
	<td> 
	<?php if ($modules[$d]['iscore']) {?><span style="color: #999">禁止</span><?php } else {?><a class="J_ajax_uninstall" href="<?php echo U('Module/uninstall', array('module'=>$d) );?>"><font color="red">卸载</font></a><?php }?>
	</td>
	</tr>
<?php  } else { $moduel = $isinstall = $modulename = ''; if (file_exists(APP_PATH . C("APP_GROUP_PATH"). DIRECTORY_SEPARATOR.$d.DIRECTORY_SEPARATOR.'Install'.DIRECTORY_SEPARATOR.'Config.inc.php')) { require APP_PATH . C("APP_GROUP_PATH"). DIRECTORY_SEPARATOR.$d.DIRECTORY_SEPARATOR.'Install'.DIRECTORY_SEPARATOR.'Config.inc.php'; $isinstall = "安装"; } else { $module = "未知"; $isinstall = "无法安装"; } ?>
	<tr class="on">
	<td width="220"><?php echo $modulename?></td>
	<td width="220"><?php echo $d?></td>
	<td align="center">未知</td>
	<td align="center">未知</td>
	<td align="center">未安装</td>
	<td>
	<?php if ($isinstall!="无法安装") {?> <a href="<?php echo U('Module/install', array('module'=>$d) );?>"><font color="#009933"><?php echo $isinstall?></font><?php } else {?><font color="#009933"><?php echo $isinstall?></font><?php }?></a>
	</td>
	</tr>
<?php  } } } ?>
        </tbody>
      </table>
      <div class="p10"><div class="pages"> <?php echo ($Page); ?> </div> </div>
  </div>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
<script>
if ($('a.J_ajax_uninstall').length) {
    Wind.use('artDialog', function () {
        $('.J_ajax_uninstall').on('click', function (e) {
            e.preventDefault();
            var $_this = this,
                $this = $($_this),
                href = $this.prop('href'),
                msg = $this.data('msg');
            art.dialog({
                title: false,
                icon: 'question',
                content: '确定要卸载吗？',
                follow: $_this,
                close: function () {
                    $_this.focus();; //关闭时让触发弹窗的元素获取焦点
                    return true;
                },
                ok: function () {
                    $.getJSON(href).done(function (data) {
                        if (data.state === 'success') {
                            if (data.referer) {
                                location.href = data.referer;
                            } else {
                                reloadPage(window);
                            }
                        } else if (data.state === 'fail') {
                            art.dialog.alert(data.info);
                        }
                    });
                },
                cancelVal: '关闭',
                cancel: true
            });
        });

    });
}
</script>
</body>
</html>