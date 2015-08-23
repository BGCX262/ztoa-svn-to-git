<?php

/**
 * 应用初始化前进行相关配置操作和初始操作
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class AppBehavior extends Behavior {

    //行为参数定义
    protected $options = array();

    public function run(&$params) {
        /**
         * 初始化网站基本配置
         */
        $Config = F("Config");
        if (!$Config) {
            $Config = D("Config")->config_cache();
        }
        //网站访问地址
        define("SITEURL", $Config['siteurl']);
        foreach ($Config as $k => $v) {
            define('CONFIG_' . strtoupper($k), $v);
        }
        //取得已安装模块缓存
        $App = F("App");
        if (!$App) {
            //生成好缓存
            D("Module")->module_cache();
            $App = F("App");
        }
        //配置已安装模块列表
        C("APP_GROUP_LIST", implode(",", $App));
        
        /**
         * 域名绑定模块相关处理
         * 由于DEFAULT_GROUP在此就要生效，所以在这里就要出来域名绑定模块相关工作。
         * 取得域名后，根据域名取得对应模块，特殊模块（内容模块，附件模块）不按此处配置。
         */
        if ($App['Domains']) {
            //开启子域名部署
            C("APP_SUB_DOMAIN_DEPLOY", true);
            //当前访问域名
            $http_host = strtolower($_SERVER['HTTP_HOST']);
            //加载缓存
            $Domains_cache = F("Domains_list");
            //========加载缓存失败，生成缓存========
            if (!$Domains_cache) {
                //查询数据库
                $db = M("Domains");
                $Domains_data = $db->where(array("status" => 1))->field(array("module", "domain"))->select();
                foreach ($Domains_data as $r) {
                    $r['domain'] = explode("|", $r['domain']);
                    $Domains_list[$r['module']] = $r['domain'][0];
                    foreach ($r['domain'] as $dom) {
                        $Domains_cache[$dom] = $r['module'];
                    }
                }
                //缓存 域名->模块
                F("Domains_list", $Domains_cache);
                //缓存 模块->绑定的域名
                F("Module_Domains_list", $Domains_list);
            }
            //========缓存生成结束========
            
            /**
             * 域名绑定模块开始处理，原理：修改TP的DEFAULT_GROUP配置来实现模块绑定二级域名。
             */
            //取得域名对应绑定模块
            $module = $Domains_cache[$http_host];
            if ($http_host && $module && !in_array($module,array("Attachment","Contents"))) {
                //设置当前模块为默认模块
                C("DEFAULT_GROUP", $module);
                C("APP_SUB_DOMAIN", $http_host);
            }else{
                //该域名没有绑定模块，启用默认模块
                C("APP_SUB_DOMAIN",false);
            }
        }
    }

}

?>
