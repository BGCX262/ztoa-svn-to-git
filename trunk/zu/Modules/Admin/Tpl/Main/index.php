<?php if (!defined('ZU_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body>
<div class="wrap">
  <div id="home_toptip"></div>
  <h2 class="h_a">系统信息</h2>
  <div class="home_info">
    <ul>
      <volist name="server_info" id="vo">
        <li> <em>{$key}</em> <span>{$vo}</span> </li>
      </volist>
    </ul>
  </div>
  <h2 class="h_a">开发团队</h2>
  <div class="home_info" id="home_devteam">
    <ul>
      <li> <em>版权所有</em> <span>www.85zu.com</span> </li>
      <li> <em>负责人</em> <span>兰斌斌</span> </li>
      <li> <em>联系邮箱</em> <span>lanbinbin@85zu.com</span> </li>
    </ul>
  </div>
</div>
<!--升级提示-->
<script src="{$config_siteurl}statics/js/common.js?v"></script> 
<script>
$("#btn_submit").click(function(){
	$("#tips_success").fadeTo(500,1);
});
</script>
</body>
</html>