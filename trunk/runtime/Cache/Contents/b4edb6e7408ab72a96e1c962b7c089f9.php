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
<body class="J_scroll_fixed" style=" padding-top:5px; padding-left:10px;">
<script type="text/javascript">
Wind.use('cookie','treeview', function(){
	$("#category_tree").treeview({
			control: "#treecontrol",
			persist: "cookie",
			cookieId: "treeview-black"
	});
});
function openwinx(url,name,w,h) {
    window.open(url);
}
</script>
<style type="text/css">
a {
   color: black;
   text-decoration: none;
}
.treeview, .treeview ul { 
	padding: 0;
	margin: 0;
	list-style: none;
}
.treeview ul {
	margin-top: 4px;
}
.treeview .hitarea {
	background: url(<?php echo ($config_siteurl); ?>statics/images/treeview-default.gif) -64px -25px no-repeat;
	height: 16px;
	width: 16px;
	margin-left: -16px;
	float: left;
	cursor: pointer;
}
.treeview .placeholder {
    background: url("<?php echo ($config_siteurl); ?>statics/images/admin_img/onLoad.gif") no-repeat scroll 0 0 transparent;
    display: block;
    height: 16px;
    width: 16px;
}
/* fix for IE6 */
* html .hitarea {
	display: inline;
	float:none;
}
.treeview li { 
	margin: 0;
	padding: 3px 0pt 3px 16px;
}
.treeview a.selected {
	background-color: #eee;
}
#treecontrol { padding-left:3px;margin: 1em 15; display: ; }
.treeview .hover { color: red; cursor: pointer; }
.treeview li { background: url(<?php echo ($config_siteurl); ?>statics/images/treeview-default-line.gif) 0 0 no-repeat; }
.treeview li.collapsable, .treeview li.expandable { background-position: 0 -176px; }
.treeview .expandable-hitarea { background-position: -80px -3px; }
.treeview li.last { background-position: 0 -1766px }
.treeview li.lastCollapsable, .treeview li.lastExpandable { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-default.gif); }  
.treeview li.lastCollapsable { background-position: 0 -111px }
.treeview li.lastExpandable { background-position: -32px -67px }
.treeview div.lastCollapsable-hitarea, .treeview div.lastExpandable-hitarea { background-position: 0; }
.treeview-red li { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-red-line.gif); }
.treeview-red .hitarea, .treeview-red li.lastCollapsable, .treeview-red li.lastExpandable { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-red.gif); } 
.treeview-black li { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-black-line.gif); }
.treeview-black .hitarea, .treeview-black li.lastCollapsable, .treeview-black li.lastExpandable { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-black.gif); }  
.treeview-gray li { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-gray-line.gif); }
.treeview-gray .hitarea, .treeview-gray li.lastCollapsable, .treeview-gray li.lastExpandable { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-gray.gif); } 
.treeview-famfamfam li { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-famfamfam-line.gif); }
.treeview-famfamfam .hitarea, .treeview-famfamfam li.lastCollapsable, .treeview-famfamfam li.lastExpandable { background-image: url(<?php echo ($config_siteurl); ?>statics/images/treeview-famfamfam.gif); } 
.filetree li { padding: 3px 0 2px 16px; }
.filetree span.folder, .filetree span.file { padding: 1px 0 1px 16px; display: block; }
.filetree span.folder { background: url(<?php echo ($config_siteurl); ?>statics/images/folder.gif) 0 0 no-repeat; }
.filetree li.expandable span.folder { background: url(<?php echo ($config_siteurl); ?>statics/images/folder-closed.gif) 0 0 no-repeat; }
.filetree span.file { background: url(<?php echo ($config_siteurl); ?>statics/images/file.gif) 0 0 no-repeat; }
 </style>
<div>
  <div id="treecontrol"> 
   <span style="display:none"> <a href="#"></a> <a href="#"></a></span> 
   <a href="#"><img src="<?php echo ($config_siteurl); ?>statics/images/minus.gif" /> <img src="<?php echo ($config_siteurl); ?>statics/images/application_side_expand.png" /> 展开/收缩</a> 
  </div>
  <ul class="filetree  treeview" style="display:none">
  <li class="collapsable">
    <div class="hitarea collapsable-hitarea"></div>
    <span><img src="<?php echo ($config_siteurl); ?>statics/images/icon/box-exclaim.gif" width="15" height="14">&nbsp;<a target='right'>审核内容</a></span>
   </li>
  </ul>
  <?php echo ($categorys); ?>
</div>
<script src="<?php echo ($config_siteurl); ?>statics/js/common.js?v"></script>
</body>
</html>