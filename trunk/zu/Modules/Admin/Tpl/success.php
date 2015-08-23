<?php if (!defined('ZU_VERSION')) exit(); ?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>{$Config.sitename} - 提示信息</title>
<Admintemplate file="Admin/Common/Cssjs"/>
</head>
<body>
<div class="wrap">
  <div id="error_tips">
    <h2>{$msgTitle}</h2>
    <div class="error_cont">
      <ul>
        <li>{$message}</li>
      </ul>
      <div class="error_return"><em id="countdown_time"><span style="color:blue;font-weight:bold">{$waitSecond/1000}</span></em>秒后自动跳转&nbsp;&nbsp;如不想等待，直接点：<a href="{$jumpUrl}" class="btn">返回</a></div>
    </div>
  </div>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
<script language="javascript">
setTimeout(function(){
	location.href = '{$jumpUrl}';
},{$waitSecond});

          var max_sec={$waitSecond/1000};
        $(document).ready(function(){
          $("#countdown_time").html(max_sec);
          window.setInterval(SetRemainTime,1000);
        });    
        function SetRemainTime(){
            if(max_sec>1){
             $("#countdown_time").html(max_sec-1);  
             max_sec--;
             }
        }
</script>
</body>
</html>