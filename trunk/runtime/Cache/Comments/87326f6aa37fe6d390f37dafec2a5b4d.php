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
  <form name="searchform" action="<?php echo ($config_siteurl); ?>index.php" method="get" >
    <input type="hidden" value="Comments" name="g">
    <input type="hidden" value="Comments" name="m">
    <input type="hidden" value="index" name="a">
    <div class="search_type cc mb10">
      <div class="mb10"> <span class="mr20"> 搜索类型：
        <select name="searchtype">
          <option value="1" <?php if($_GET['searchtype'] == '1'): ?>selected<?php endif; ?>>评论作者</option>
          <option value="2" <?php if($_GET['searchtype'] == '2'): ?>selected<?php endif; ?> >所属文章id</option>
        </select>
        关键字：
        <input type="text" class="input length_2" name="keyword" size='10' value="<?php echo ($_GET['keyword']); ?>" placeholder="关键字">
        <button class="btn">搜索</button>
        </span> </div>
    </div>
  </form>
  <form name="myform"  class="J_ajaxForm" action="" method="post" >
    <div class="table_list">
      <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td width="20" align="center"><input type="checkbox" class="J_check_all" data-direction="x" data-checklist="J_check_x"></td>
            <td width="50" align="center">ID</td>
            <td width="100" align="center">作者</td>
            <td >评论内容</td>
            <td width="180" align="center">原文标题</td>
            <td width="180" align="center">操作</td>
          </tr>
        </thead>
        <tbody>
          <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
              <td align="center"><input class="input checkbox J_check "  data-yid="J_check_y" data-xid="J_check_x"  name="ids[]" value="<?php echo ($vo["id"]); ?>" type="checkbox"></td>
              <td align="center"><?php echo ($vo["id"]); ?></td>
              <td align="center"><?php echo ($vo["author"]); ?></td>
              <td ><?php echo ($vo["content"]); ?><br/>
                <b>发表时间：<?php echo (date("Y-m-d H:i:s",$vo["date"])); ?>，IP：<?php echo ($vo["author_ip"]); ?></b></td>
              <td align="center"><a href="<?php echo ($vo["url"]); ?>" target="_blank"><?php echo ($vo["title"]); ?></a></td>
              <td align="center"><a class="J_ajax_del" href="<?php echo U("Comments/delete",array("id"=>$vo['id']));?>">删除</a> | <a href="<?php echo U("Comments/edit",array("id"=>$vo['id']));?>">编辑</a> | <a href="<?php echo U("Comments/replycomment",array("id"=>$vo['id']));?>">回复</a> | <a href="<?php echo U("Comments/spamcomment",array("id"=>$vo['id']));?>">取消审核</a></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
      </table>
      <div class="p10">
        <div class="pages"> <?php echo ($Page); ?> </div>
      </div>
    </div>
    <div class="">
      <div class="btn_wrap_pd">
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">删除</button>
      </div>
    </div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>