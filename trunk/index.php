<?php

/**
 * 项目入口文件
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
//开启调试模式
define("APP_DEBUG", true);
//网站当前路径
define('SITE_PATH', getcwd());
//项目名称，不可更改
define('APP_NAME', 'zu');
//项目路径，不可更改
define('APP_PATH', SITE_PATH . '/zu/');
//定义缓存存放路径
define("RUNTIME_PATH", SITE_PATH . "/runtime/");
//模板路径
define('TEMPLATE_PATH', APP_PATH . 'Template/');
//版本号
define("ZU_VERSION", '20130327');
//大小写忽略处理
foreach (array("g", "m") as $v) {
    if (isset($_GET[$v])) {
        $_GET[$v] = ucwords($_GET[$v]);
    }
}
set_time_limit(0);
if (!file_exists(APP_PATH.'Conf/dataconfig.php')) {
    header("Location: install/");
    exit;
}
//载入框架核心文件
require APP_PATH.'Core/ThinkPHP.php';
?>
