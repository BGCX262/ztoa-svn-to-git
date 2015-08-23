<?php

/**
 * 应用开始处理
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class AppcheckBehavior extends Behavior {

    public function run(&$params) {
        //当前访问模块状态，默认false
        $status = false;
        //已安装模块缓存
        $App = F("App");
        //当前域名
        $http_host = strtolower($_SERVER['HTTP_HOST']);
        //域名绑定模块缓存
        $Module_Domains_list = F("Module_Domains_list");
        //网站配置缓存
        $Config = F("Config");
        //后台模块比较特殊，可以指定域名访问，其他模块不需要经过此步骤
        if (  'Admin' == GROUP_NAME && $App['Domains'] ){
            if((int)$Config['domainaccess']){
                $domain = $Module_Domains_list["Admin"];
                $domain = explode("|", $domain);
                if($Module_Domains_list["Admin"] && $http_host != $domain[0]){
                    //后台不是用指定域名访问，直接404！
                    send_http_status(404);
                    exit;
                }
            }
        }
        
//        //如果模块有绑定域名，将以绑定域名的形式进行访问。post提交 除外
//        if ($App['Domains'] && !IS_POST) {
//            //获取当前模块的域名绑定信息
//            $domain = $Module_Domains_list[GROUP_NAME];
//            //存在表示该模块有域名绑定
//            if ($domain) {
//                //由于支持多域名绑定，以“|”分割成数组
//                $domain = explode("|", $domain);
//                //检查当前访问的域名是否在绑定域名数组中，不在，404
//                if ( !in_array($http_host,$domain) ) {
//                    send_http_status(404);
//                    exit;
//                    //发送301状态
//                    send_http_status(301);
//                    //进行URL重组
//                    $url = GROUP_NAME . "/" . MODULE_NAME . "/" . ACTION_NAME . "@" . $domain[0];
//                    if (in_array(C('URL_MODEL'), array(1,2,3))) {
//                        unset($_GET['_URL_']);
//                    }
//                    //重定位
//                    header('Location: ' . U($url, $_GET));
//                    exit;
//                }
//            }
//        }
        //判断当前访问的模块是否在已安装模块列表中
        if ($App) {
            if (!in_array(GROUP_NAME, $App)) {
                $status = false;
            }else{
                $status = true;
            }
        } else {
            $disabled = M("Module")->where(array("disabled", "module" => GROUP_NAME))->getField("disabled");
            if (!$disabled) {
                $status = false;
            }else{
                $status = true;
            }
            //生成缓存
            D("Module")->module_cache();
        }

        if ( false === $status ) {
            $msg = L('_MODULE_NOT_EXIST_') . MODULE_NAME . "，该模块未进行安装！";
            if (APP_DEBUG) {
                // 模块不存在 抛出异常
                throw_exception($msg);
            } else {
                if (C('LOG_RECORD'))
                    Log::write($msg."URL：".get_url());
                send_http_status(404);
                exit;
            }
        }
    }

}

?>
