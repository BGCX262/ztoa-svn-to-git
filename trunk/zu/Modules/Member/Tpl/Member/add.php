<?php if (!defined('ZU_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">添加会员</div>
  <form name="myform" action="{:U('Member/add')}" method="post" class="J_ajaxForm">
  <div class="table_full">
  <table width="100%" class="table_form">
		<tr>
			<th width="80">用户名</th> 
			<td><input type="text" name="username"  class="input" id="username"></input></td>
		</tr>
		<tr>
			<th>是否审核</th> 
			<td><input name="checked" type="radio" value="1" checked  />审核通过 <label class="type"><input name="checked" type="radio" value="0"   />待审核</td>
		</tr>
		<tr>
			<th>密码</th> 
			<td><input type="password" name="password" class="input" id="password" value=""></input></td>
		</tr>
		<tr>
			<th>确认密码</th> 
			<td><input type="password" name="pwdconfirm" class="input" id="pwdconfirm" value=""></input></td>
		</tr>
		<tr>
			<th>昵称</th> 
			<td><input type="text" name="nickname" id="nickname" value="" class="input"></input></td>
		</tr>
		<tr>
			<th>邮箱</th>
			<td>
			<input type="text" name="email" value="" class="input" id="email" size="30"></input>
			</td>
		</tr>
		<tr>
			<th>会员组</th>
			<td><?php echo Form::select($Member_group, (int)$_GET['groupid'], 'name="groupid"', '') ?></td>
		</tr>
		<tr>
			<th>积分点数</th>
			<td>
			<input type="text" name="point" value="" class="input" id="point" size="10"></input>
			</td>
		</tr>
		<tr>
			<th>会员模型</th>
			<td><?php echo Form::select($Model_Member, 0, 'name="modelid"', ''); ?></td>
		</tr>
	</table>
  </div>
   <div class="">
      <div class="btn_wrap_pd">             
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">添加</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>