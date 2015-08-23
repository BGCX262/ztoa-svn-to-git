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
  <form name="searchform" action="" method="get" >
    <input type="hidden" value="Member" name="g">
    <input type="hidden" value="Member" name="m">
    <input type="hidden" value="index" name="a">
    <input type="hidden" value="1" name="search">
    <div class="search_type cc mb10">
      <div class="mb10"> <span class="mr20"> 注册时间：
        <input type="text" name="start_time" class="input length_2 J_date" value="<?php echo ($_GET['start_time']); ?>" style="width:80px;">
        -
        <input type="text" class="input length_2 J_date" name="end_time" value="<?php echo ($_GET['end_time']); ?>" style="width:80px;">
        <select name="status">
          <option value='0' >状态</option>
          <option value='1' >锁定</option>
          <option value='2' >正常</option>
        </select>
        <?php echo Form::select($Member_group, (int)$_GET['groupid'], 'name="groupid"', '会员组') ?>
        <?php echo Form::select($Model_Member, (int)$_GET['modelid'], 'name="modelid"', '会员模型'); ?>
        <select name="type">
          <option value='1' >用户名</option>
          <option value='2' >用户ID</option>
          <option value='3' >邮箱</option>
          <option value='4' >注册ip</option>
          <option value='5' >昵称</option>
        </select>
        <input name="keyword" type="text" value="<?php echo ($_GET['keyword']); ?>" class="input" />
        <button class="btn">搜索</button>
        </span> </div>
    </div>
  </form>
  <form name="myform" action="<?php echo U('Member/delete');?>" method="post" class="J_ajaxForm">
    <div class="table_list">
      <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td  align="left" width="20"><input type="checkbox" class="J_check_all" data-direction="x" data-checklist="J_check_x"></td>
            <td align="left"></td>
            <td align="left">用户ID</td>
            <td align="left">用户名</td>
            <td align="left">昵称</td>
            <td align="left">邮箱</td>
            <td align="left">模型名称</td>
            <td align="left">注册ip</td>
            <td align="left">最后登录</td>
            <td align="left">金钱总数</td>
            <td align="left">积分点数</td>
            <td align="left">操作</td>
          </tr>
        </thead>
        <tbody>
          <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
              <td align="left"><input type="checkbox" class="J_check" data-yid="J_check_y" data-xid="J_check_x"  value="<?php echo ($vo["userid"]); ?>" name="userid[]"></td>
              <td align="left"><?php if( $vo['islock'] == '1' ): ?><img title="锁定" src="<?php echo ($config_siteurl); ?>statics/images/icon/icon_padlock.gif"><?php endif; ?>
                <?php if( $vo['checked'] == '0' ): ?><img title="待审核" src="<?php echo ($config_siteurl); ?>statics/images/icon/info.png"><?php endif; ?></td>
              <td align="left"><?php echo ($vo["userid"]); ?></td>
              <td align="left"><img src="<?php echo getavatar($vo['userid']);?>" height=18 width=18 onerror="this.src='<?php echo ($config_siteurl); ?>statics/images/member/nophoto.gif'"><?php echo ($vo["username"]); ?><a href="javascript:member_infomation(<?php echo ($vo["userid"]); ?>, '<?php echo ($vo["modelid"]); ?>', '')"><img src="<?php echo ($config_siteurl); ?>statics/images/icon/detail.png"></a></td>
              <td align="left"><?php echo ($vo["nickname"]); ?></td>
              <td align="left"><?php echo ($vo["email"]); ?></td>
              <td align="left"><?php echo ($Model_Member[$vo['modelid']]); ?></td>
              <td align="left"><?php echo ($vo["regip"]); ?></td>
              <td align="left"><?php if( $vo['lastdate'] == 0 ): ?>还没有登录过
                  <?php else: ?>
                  <?php echo (date('Y-m-d H:i:s',$vo["lastdate"])); endif; ?></td>
              <td align="left"><?php echo ($vo["amount"]); ?></td>
              <td align="left"><?php echo ($vo["point"]); ?></td>
              <td align="left"><a href="<?php echo U('Member/edit', array('userid'=>$vo['userid']) );?>">[修改]</a></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
      </table>
      <div class="p10">
        <div class="pages"> <?php echo ($Page); ?> </div>
      </div>
    </div>
    <div class="">
      <div class="btn_wrap_pd">
        <button class="btn  mr10 J_ajax_submit_btn" data-action="<?php echo U('Member/Member/lock');?>" type="submit">锁定</button>
        <button class="btn  mr10 J_ajax_submit_btn" data-action="<?php echo U('Member/Member/unlock');?>" type="submit">解锁</button>
        <button class="btn  mr10 J_ajax_submit_btn" type="submit">删除</button>
      </div>
    </div>
  </form>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
<script src="<?php echo ($config_siteurl); ?>statics/js/content_addtop.js"></script>
<script>
//会员信息查看
function member_infomation(userid, modelid, name) {
	omnipotent("member_infomation", GV.DIMAUB+'index.php?g=Member&m=Member&a=memberinfo&userid='+userid+'', "个人信息",1)
}
</script>
</body>
</html>