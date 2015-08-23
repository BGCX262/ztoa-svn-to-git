<?php 
/**
 * 模块卸载，菜单/权限配置
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
defined('UNINSTALL') or exit('Access Denied');
//删除菜单/权限数据
M("Menu")->where(array("app"=>"Search"))->delete();
M("Access")->where(array("g" => "Search"))->delete();
?>