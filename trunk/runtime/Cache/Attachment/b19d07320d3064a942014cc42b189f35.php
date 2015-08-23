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
  <div class="h_a">搜索</div>
  <form method="get" action="">
    <input type="hidden" value="Attachment" name="g">
    <input type="hidden" value="Atadmin" name="m">
    <input type="hidden" value="index" name="a">
    <div class="search_type cc mb10">
      <div class="mb10"> <span class="mr20">
      上传时间：
        <input type="text" name="start_uploadtime" class="input length_2 J_date" value="<?php echo ($start_uploadtime); ?>" style="width:80px;">
        -
        <input type="text" class="input length_2 J_date" name="end_uploadtime" value="<?php echo ($end_uploadtime); ?>" style="width:80px;">
        附件类型：
        <input type="text" class="input length_2" name="fileext" style="width:80px;" value="<?php echo ($fileext); ?>" placeholder="类型：jpg、png">
        使用状态：
        <select name="status" id="status" >
            <option value="" <?php if($status == '' ): ?>selected<?php endif; ?>>全部</option>
            <option value="1" <?php if($status == '1' ): ?>selected<?php endif; ?>>已经在使用</option>
            <option value="0" <?php if($status == '0' ): ?>selected<?php endif; ?>>没有被使用</option>
          </select> 
          附件名称：
        <input type="text" class="input length_2" name="keyword" style="width:200px;" value="<?php echo ($filename); ?>" placeholder="请输入附件名称...">
        <button class="btn">搜索</button>
        </span> </div>
    </div>
  </form>
  <form name="myform" action="<?php echo U("Atadmin/delete");?>" method="post" class="J_ajaxForm">
  <div class="table_list">
  <table width="100%" cellspacing="0">
      <thead>
        <tr>
          <td width="45" align="center"><input type="checkbox" class="J_check_all" data-direction="x" data-checklist="J_check_x">全选</td>
          <td width="40" align="center">ID</td>
          <td width="70" align="center" >上传用户ID </td>
          <td width="100" align="center" >栏目名称</td>
          <td>附件名称</td>
        <td width="100" align="center">附件大小</td>
        <td width="200" align="center">上传时间</td>
        <td width="200" align="center">管理操作</td>
      </tr>
      </thead>
      <tbody>
      <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
          <td align="center"><input type="checkbox" class="J_check" data-yid="J_check_y" data-xid="J_check_x" name="aid[]" value="<?php echo ($vo["aid"]); ?>" id="att_<?php echo ($vo["aid"]); ?>" /></td>
          <td align="center"><?php echo ($vo["aid"]); ?></td>
          <td align="center"><?php if( $vo['isadmin'] ): ?>[后台]<?php endif; if( !$vo['userid'] ): ?>游客<?php else: echo ($vo["userid"]); endif; ?></td>
          <td align="center"><?php echo ($category[$vo['catid']]['catname']); ?></td>
          <td ><img src="<?php echo ($config_siteurl); ?>statics/images/ext/<?php echo ($vo["fileext"]); ?>.gif" /><?php echo ($vo["filename"]); ?> 
          <?php if( $vo['thumb'] ): ?><img title="管理缩略图" src="<?php echo ($config_siteurl); ?>statics/images/icon/havthumb.png" onClick="showthumb(<?php echo ($vo["aid"]); ?>, '<?php echo ($vo["filename"]); ?>')"/><?php endif; ?>
          <?php if( $vo['status'] ): ?><img title="该附件已被使用" src="<?php echo ($config_siteurl); ?>statics/images/icon/alink.png"/><?php endif; ?>
          </td>
          <td align="center"><?php echo ($vo["filesize"]); ?> KB</td>
          <td align="center"><?php echo ($vo["uploadtime"]); ?></td>
          <td align="center"><a href="javascript:preview(<?php echo ($vo["aid"]); ?>, '<?php echo ($vo["filename"]); ?>','<?php echo CONFIG_SITEFILEURL; echo ($vo["filepath"]); ?>')">预览</a> | <a class="J_ajax_del" href="<?php echo U('Attachment/Atadmin/delete',array('aid'=>$vo['aid']));?>">删除</a></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
      </tbody>
    </table>
    <div class="p10">
        <div class="pages"> <?php echo ($Page); ?> </div>
      </div>
  </div>
  <div class="">
      <div class="btn_wrap_pd">
        <label class="mr20"><input type="checkbox" class="J_check_all" data-direction="y" data-checklist="J_check_y">全选</label> 
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">删除附件</button>
      </div>
    </div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
<script src="<?php echo ($config_siteurl); ?>statics/js/content_addtop.js?v"></script>
<script type="text/javascript">
//附件预览
function preview(id, name, filepath) {
    if (IsImg(filepath)) {
        Wind.use('artDialog', 'imgready', function () {
            imgReady(filepath, function () {
                var width = 0,
                    maxWidth = 500,
                    maxHeight = 500,
                    height = 0;
                var hRatio;
                var wRatio;
                var Ratio = 1;
                var w = this.height;
                var h = this.width;
                wRatio = 500 / this.height;
                hRatio = 500 / this.width;

                if (maxWidth == 0) { //
                    if (hRatio < 1) Ratio = hRatio;
                } else if (maxHeight == 0) {
                    if (wRatio < 1) Ratio = wRatio;
                } else if (wRatio < 1 || hRatio < 1) {
                    Ratio = (wRatio <= hRatio ? wRatio : hRatio);
                }
                if (Ratio < 1) {
                    w = w * Ratio;
                    h = h * Ratio;
                }
                width = h;
                height = w;

                art.dialog({
                    title: '预览',
                    fixed: true,
                    width: width,
                    height: height,
                    id: "image_priview",
                    lock: true,
                    background: "#CCCCCC",
                    opacity: 0,
                    content: '<img src="' + filepath + '"  width="' + width + '" height="' + height + '" />'
                });

            });
        });
    } else {
        Wind.use('artDialog', function () {
            art.dialog({
                title: '预览',
                padding: 0,
                width: 230,
                height: 150,
                content: '<a href="' + filepath + '" target="_blank"><img src="<?php echo ($config_siteurl); ?>statics/images/icon/down.gif">单击打开</a>',
                lock: true
            });
        });
    }
}

//缩图管理
function showthumb(id, name) {
    Wind.use('artDialog', 'iframeTools', function () {
        art.dialog.open(GV.DIMAUB + 'index.php?a=pullic_showthumbs&m=Attachments&g=Attachment&aid=' + id, {
            title: '管理缩略图--' + name,
            padding: 0,
            width: '500px',
            height: '400px',
            lock: true,
            background: "#CCCCCC",
            opacity: 0
        });
    });
}
</script>
</body>
</html>