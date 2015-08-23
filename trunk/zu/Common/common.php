<?php

/* * 
 * 项目扩展函数库
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */

// 调用接口服务
function X($name, $params = array(), $domain = 'Service') {
    //创建一个静态变量，用于缓存实例化的对象
    static $_service = array();
    $app = C('DEFAULT_APP');
    //如果已经实例化过，则返回缓存实例化对象
    if (isset($_service[$domain . '_' . $app . '_' . $name]))
        return $_service[$domain . '_' . $app . '_' . $name];
    //载入文件
    $class = $name . $domain;
    import("{$domain}.{$name}{$domain}", APP_PATH . 'Lib');
    //服务不可用时 记录日志 或 抛出异常
    if (class_exists($class)) {
        $obj = new $class($params);
        $_service[$domain . '_' . $app . '_' . $name] = $obj;
        return $obj;
    } else {
        return false;
    }
}

/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[分组/模块/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $redirect 是否跳转，如果设置为true则表示跳转到该URL地址
 * @param boolean $domain 是否显示域名
 * @return string
 */
function U($url = '', $vars = '', $suffix = true, $redirect = false, $domain = true) {
    static $_ucache = array();
    //标识
    $key = md5($url . (is_array($vars) ? implode("+", $vars) : $vars) . $suffix . $domain);
    if (isset($_ucache[$key])) {
        if ($redirect) {
            redirect($_ucache[$key]);
        } else {
            return $_ucache[$key];
        }
    }
    // 解析URL
    $info = parse_url($url);
    $url = !empty($info['path']) ? $info['path'] : ACTION_NAME;
    if (isset($info['fragment'])) { // 解析锚点
        $anchor = $info['fragment'];
        if (false !== strpos($anchor, '?')) { // 解析参数
            list($anchor, $info['query']) = explode('?', $anchor, 2);
        }
        if (false !== strpos($anchor, '@')) { // 解析域名
            list($anchor, $host) = explode('@', $anchor, 2);
        }
    } elseif (false !== strpos($url, '@')) { // 解析域名
        list($url, $host) = explode('@', $info['path'], 2);
    }
    $config_url = parse_url(CONFIG_SITEURL);
    // 解析子域名
    if (isset($host)) {
        $domain = $host . (strpos($host, '.') ? '' : strstr($_SERVER['HTTP_HOST'], '.'));
    } elseif ($domain === true) {
        $domain = strtolower($config_url['host']);
        // 开启子域名部署
        if (C('APP_SUB_DOMAIN_DEPLOY')) {
            $App = F("App");
            if ($App['Domains']) {
                $Domains_list = F("Domains_list");
                $Module_Domains_list = F("Module_Domains_list");
                $Domains_module = ucwords($Domains_list[$domain]);
                $path_list = explode(C('URL_PATHINFO_DEPR'), $url);
                if (count($path_list) < 3) {
                    array_unshift($path_list, GROUP_NAME);
                }

                if ($Domains_module && !in_array($Domains_module, array("Attachment", "Contents")) && $Domains_module == $path_list[0]) {
                    $domain = strtolower($_SERVER['HTTP_HOST']);
                } else {
                    $domain = C("APP_SUB_DOMAIN"); // 生成对应子域名
                    if ($Module_Domains_list[$path_list[0]]) {
                        $domain = explode("|", $Module_Domains_list[$path_list[0]]);
                        $domain = $domain[0]; // 生成对应子域名
                        //当前模块有绑定域名
                        $_domain = true;
                    }
                    if (false == $domain) {
                        $domain = strtolower($config_url['host']);
                    }
                }
            } else {
                $domain = $config_url['host']; // 生成对应子域名
            }

            //由于前台模块不能绑定域名，但后台有绑定域名访问时，会出现域名混乱，当有定义该常量时，没有绑定域名的模块，生成地址以网站配置域名为主
            if (defined("APP_SUB_DOMAIN_NO") && APP_SUB_DOMAIN_NO == 1) {
                if (!isset($_domain)) {
                    $domain = $config_url['host'];
                }
            }
        }
        
        //端口号处理
        if ($domain) {
            if (isset($config_url['port']) && $config_url['port'] && (int)$config_url['port'] != 80) {
                $domain .= ":" . $config_url['port'];
            }
        }
        
    }

    // 解析参数
    if (is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars, $vars);
    } elseif (!is_array($vars)) {
        $vars = array();
    }
    if (isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'], $params);
        $vars = array_merge($params, $vars);
    }

    // URL组装
    $depr = C('URL_PATHINFO_DEPR');
    if ($url) {
        if (0 === strpos($url, '/')) {// 定义路由
            $route = true;
            $url = substr($url, 1);
            if ('/' != $depr) {
                $url = str_replace('/', $depr, $url);
            }
        } else {
            if ('/' != $depr) { // 安全替换
                $url = str_replace('/', $depr, $url);
            }
            // 解析分组、模块和操作
            $url = trim($url, $depr);
            $path = explode($depr, $url);
            $var = array();
            $var[C('VAR_ACTION')] = !empty($path) ? array_pop($path) : ACTION_NAME;
            $var[C('VAR_MODULE')] = !empty($path) ? array_pop($path) : MODULE_NAME;
            if ($maps = C('URL_ACTION_MAP')) {
                if (isset($maps[strtolower($var[C('VAR_MODULE')])])) {
                    $maps = $maps[strtolower($var[C('VAR_MODULE')])];
                    if ($action = array_search(strtolower($var[C('VAR_ACTION')]), $maps)) {
                        $var[C('VAR_ACTION')] = $action;
                    }
                }
            }
            if ($maps = C('URL_MODULE_MAP')) {
                if ($module = array_search(strtolower($var[C('VAR_MODULE')]), $maps)) {
                    $var[C('VAR_MODULE')] = $module;
                }
            }
            if (C('URL_CASE_INSENSITIVE')) {
                $var[C('VAR_MODULE')] = parse_name($var[C('VAR_MODULE')]);
            }
            //修改，当启动模块绑定独立域名时，访问其他模块时，参数带上g=xx
            if (($path[0] != GROUP_NAME || C("DEFAULT_GROUP") != GROUP_NAME) && !isset($_domain)) {

                if (!empty($path)) {
                    $group = array_pop($path);
                    $var[C('VAR_GROUP')] = $group;
                } else {
                    if (GROUP_NAME != C('DEFAULT_GROUP')) {
                        $var[C('VAR_GROUP')] = GROUP_NAME;
                    }
                }
                if (C('URL_CASE_INSENSITIVE') && isset($var[C('VAR_GROUP')])) {
                    $var[C('VAR_GROUP')] = strtolower($var[C('VAR_GROUP')]);
                }
            }
        }
    }

    if (C('URL_MODEL') == 0) { // 普通模式URL转换
        $url = strip_tags(_PHP_FILE_) . '?' . http_build_query(array_reverse($var));
        if (!empty($vars)) {
            $vars = urldecode(http_build_query($vars));
            $url .= '&' . $vars;
        }
    } else { // PATHINFO模式或者兼容URL模式
        if (isset($route)) {
            $url = __APP__ . '/' . rtrim($url, $depr);
        } else {
            $url = __APP__ . '/' . implode($depr, array_reverse($var));
        }
        if (!empty($vars)) { // 添加参数
            foreach ($vars as $var => $val) {
                if ('' !== trim($val))
                    $url .= $depr . $var . $depr . urlencode($val);
            }
        }
        if ($suffix) {
            $suffix = $suffix === true ? C('URL_HTML_SUFFIX') : $suffix;
            if ($pos = strpos($suffix, '|')) {
                $suffix = substr($suffix, 0, $pos);
            }
            if ($suffix && '/' != substr($url, -1)) {
                $url .= '.' . ltrim($suffix, '.');
            }
        }
    }
    if (isset($anchor)) {
        $url .= '#' . $anchor;
    }
    if ($domain) {
        $url = (is_ssl() ? 'https://' : 'http://') . $domain . $url;
    }
    $_ucache[$key] = $url;
    if ($redirect) // 直接跳转URL
        redirect($_ucache[$key]);
    else
        return $_ucache[$key];
}

// 实例化服务
function service($name, $params = array()) {
    if (strtolower($name) == 'passport') {
        $name = C("INTERFACE_PASSPORT");
        if (!$name) {
            $name = "Passport";
        }
    }
    return X($name, $params, 'Service');
}

//调用TagLib类
function TagLib($name, $params = array()) {
    return X($name, $params = array(), 'TagLib');
}

//Cookie 设置、获取、删除 
function SiteCookie($name, $value = '', $option = null) {
    // 默认设置
    $config = array(
        'prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
        'path' => C('COOKIE_PATH'), // cookie 保存路径
        'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        $value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; // 获取指定Cookie
        return authcode($value, "DECODE", C("AUTHCODE"));
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            //$value 加密
            $value = authcode($value, "", C("AUTHCODE"));
            // 设置cookie
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

//根据用户ID获取用户头像
function getavatar($uid) {
    return service("Passport")->user_getavatar($uid);
}

/**
 * 邮件发送
 * @param type $address 接收人 单个直接邮箱地址，多个可以使用数组
 * @param type $title 邮件标题
 * @param type $message 邮件内容
 */
function SendMail($address, $title, $message) {
    if (CONFIG_MAIL_PASSWORD == "") {
        return false;
    }
    import('PHPMailer');
    try {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet = C("DEFAULT_CHARSET");
        $mail->IsHTML(true);
        // 添加收件人地址，可以多次使用来添加多个收件人
        if (is_array($address)) {
            foreach ($address as $k => $v) {
                if (is_array($v)) {
                    $mail->AddAddress($v[0], $v[1]);
                } else {
                    $mail->AddAddress($v);
                }
            }
        } else {
            $mail->AddAddress($address);
        }
        // 设置邮件正文
        $mail->Body = $message;
        // 设置邮件头的From字段。
        $mail->From = CONFIG_MAIL_FROM;
        // 设置发件人名字
        $mail->FromName = CONFIG_MAIL_FNAME;
        // 设置邮件标题
        $mail->Subject = $title;
        // 设置SMTP服务器。
        $mail->Host = CONFIG_MAIL_SERVER;
        // 设置为“需要验证”
        if (CONFIG_MAIL_AUTH == '1') {
            $mail->SMTPAuth = true;
        } else {
            $mail->SMTPAuth = false;
        }
        // 设置用户名和密码。
        $mail->Username = CONFIG_MAIL_USER;
        $mail->Password = CONFIG_MAIL_PASSWORD;
        return $mail->Send();
    } catch (phpmailerException $e) {
        return $e->errorMessage();
    }
}

/**
 * 使用递归的方式删除
 * @param type $value
 * @return type
 */
function stripslashes_deep($value) {
    if (is_array($value)) {
        $value = array_map('stripslashes_deep', $value);
    } elseif (is_object($value)) {
        $vars = get_object_vars($value);
        foreach ($vars as $key => $data) {
            $value->{$key} = stripslashes_deep($data);
        }
    } else {
        $value = stripslashes($value);
    }

    return $value;
}

/**
 * 加密解密
 * @param type $string 明文 或 密文  
 * @param type $operation DECODE表示解密,其它表示加密  
 * @param type $key 密匙  
 * @param type $expiry 密文有效期  
 * @return string 
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;
    // 密匙
    $key = md5(($key ? $key : C("AUTHCODE")));
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确  
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性  
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性  
        // 验证数据有效性，请看未加密明文的格式  
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码  
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 生成上传附件验证
 * @param $args   参数
 */
function upload_key($args) {
    $auth_key = md5(C("AUTHCODE") . $_SERVER['HTTP_USER_AGENT']);
    $authkey = md5($args . $auth_key);
    return $authkey;
}

/*
 * 产生随机字符串 
 * 产生一个指定长度的随机字符串,并返回给用户 
 * @access public 
 * @param int $len 产生字符串的位数 
 * @return string 
 */

function genRandomString($len = 6,$type = 1) {
    if($type == 1){
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
    }else{
        $chars = array(
             "1", "2","3", "4", "5", "6", "7", "8", "9"
        );
    }
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱 
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 生成4位随机数字密码
 */
 function autopwd() {
    $unique = substr(mktime() * rand(),0,4);
    return $unique;
}

/**
 * 去一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "") {
    $result = array();
    foreach ($pArray as $temp_array) {
        if (is_object($temp_array)) {
            $temp_array = (array) $temp_array;
        }
        if (("" != $pCondition && $temp_array[$pCondition[0]] == $pCondition[1]) || "" == $pCondition) {
            $result[] = ("" == $pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
        }
    }
    return $result;
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
    $strlen = strlen($string);
    if ($strlen <= $length)
        return $string;
    $string = str_replace(array(' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if (strtolower(C("DEFAULT_CHARSET")) == 'utf-8') {
        $length = intval($length - strlen($dot) - $length / 3);
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵');
        $replace_arr = array('&amp;', '&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', ' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut . $dot;
}

/**
 * flash上传初始化
 * 初始化swfupload上传中需要的参数
 * @param $module 模块名称
 * @param $catid 栏目id
 * @param $args 传递参数
 * @param $userid 用户id
 * @param $groupid 用户组id 默认游客
 * @param $isadmin 是否为管理员模式
 */
function initupload($module, $catid, $args, $userid, $groupid = '8', $isadmin = false) {
    //检查用户组上传权限
    if (!$isadmin) {
        $Member_group = F("Member_group");
        if ((int) $Member_group[$groupid]['allowattachment'] < 1 || empty($Member_group)) {
            return false;
        }
    }

    $sess_id = time();
    $swf_auth_key = md5(C("AUTHCODE") . $sess_id);

    //同时允许的上传个数, 允许上传的文件类型, 是否允许从已上传中选择, 图片高度, 图片宽度,是否添加水印1是
    $args = explode(',', $args);
    //参数补充完整
    if (empty($args[1])) {
        //如果允许上传的文件类型为空，启用网站配置的 uploadallowext
        if ($isadmin) {
            $args[1] = CONFIG_UPLOADALLOWEXT;
        } else {
            $args[1] = CONFIG_QTUPLOADALLOWEXT;
        }
    }
    //允许上传后缀处理
    $arr_allowext = explode('|', $args[1]);
    foreach ($arr_allowext as $k => $v) {
        $v = '*.' . $v;
        $array[$k] = $v;
    }
    $upload_allowext = implode(';', $array);

    //允许上传大小
    if ($isadmin) {
        $file_size_limit = intval(CONFIG_UPLOADMAXSIZE);
    } else {
        $file_size_limit = intval(CONFIG_QTUPLOADMAXSIZE);
    }
    //上传个数
    $file_upload_limit = intval($args[0]) ? intval($args[0]) : '8';

    $init = 'var swfu = \'\';
	$(document).ready(function(){
		Wind.use("swfupload",GV.DIMAUB+"statics/js/swfupload/handlers.js",function(){
		      swfu = new SWFUpload({
			flash_url:"' . CONFIG_SITEURL . 'statics/js/swfupload/swfupload.swf?"+Math.random(),
			upload_url:"' . CONFIG_SITEURL . 'index.php?m=Attachments&g=Attachment&a=swfupload",
			file_post_name : "Filedata",
			post_params:{
			                        "SWFUPLOADSESSID":"' . $sess_id . '",
			                        "module":"' . $module . '",
			                        "catid":"' . $catid . '",
			                        "uid":"' . $userid . '",
			                        "isadmin":"' . $isadmin . '",
			                        "groupid":"' . $groupid . '",
			                        "thumb_width":"' . intval($args[3]) . '",
			                        "thumb_height":"' . intval($args[4]) . '",
			                        "watermark_enable":"' . (($args[5] == '') ? 1 : intval($args[5])) . '",
			                        "filetype_post":"' . $args[1] . '",
			                        "swf_auth_key":"' . $swf_auth_key . '"
			},
			file_size_limit:"' . $file_size_limit . 'KB",
			file_types:"' . $upload_allowext . '",
			file_types_description:"All Files",
			file_upload_limit:"' . $file_upload_limit . '",
			custom_settings : {progressTarget : "fsUploadProgress",cancelButtonId : "btnCancel"},
	 
			button_image_url: "",
			button_width: 75,
			button_height: 28,
			button_placeholder_id: "buttonPlaceHolder",
			button_text_style: "",
			button_text_top_padding: 3,
			button_text_left_padding: 12,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,

			file_dialog_start_handler : fileDialogStart,
			file_queued_handler : fileQueued,
			file_queue_error_handler:fileQueueError,
			file_dialog_complete_handler:fileDialogComplete,
			upload_progress_handler:uploadProgress,
			upload_error_handler:uploadError,
			upload_success_handler:uploadSuccess,
			upload_complete_handler:uploadComplete
		      });
		});
	})';
    return $init;
}

/**
 * 取得文件扩展
 * 
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 返回附件类型图标
 * @param $file 附件名称
 * @param $type png为大图标，gif为小图标
 */
function file_icon($file, $type = 'png') {
    $ext_arr = array('doc', 'docx', 'ppt', 'xls', 'txt', 'pdf', 'mdb', 'jpg', 'gif', 'png', 'bmp', 'jpeg', 'rar', 'zip', 'swf', 'flv');
    $ext = fileext($file);
    if ($type == 'png') {
        if ($ext == 'zip' || $ext == 'rar')
            $ext = 'rar';
        elseif ($ext == 'doc' || $ext == 'docx')
            $ext = 'doc';
        elseif ($ext == 'xls' || $ext == 'xlsx')
            $ext = 'xls';
        elseif ($ext == 'ppt' || $ext == 'pptx')
            $ext = 'ppt';
        elseif ($ext == 'flv' || $ext == 'swf' || $ext == 'rm' || $ext == 'rmvb')
            $ext = 'flv';
        else
            $ext = 'do';
    }

    if (in_array($ext, $ext_arr)) {
        return CONFIG_SITEURL . 'statics/images/ext/' . $ext . '.' . $type;
    } else {
        return CONFIG_SITEURL . 'statics/images/ext/blank.' . $type;
    }
}

/**
 * 判断是否为图片
 */
function is_image($file) {
    $ext_arr = array('jpg', 'gif', 'png', 'bmp', 'jpeg', 'tiff');
    $ext = fileext($file);
    return in_array($ext, $ext_arr) ? $ext_arr : false;
}

/**
 * 调试，用于保存数组到txt文件 正式生产删除 array2file($info, SITE_PATH.'/post.txt');
 */
function array2file($array, $filename) {
    file_exists($filename) or touch($filename);
    file_put_contents($filename, var_export($array, TRUE));
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    $string = str_replace('\\', '', $string);
    return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
    if (!is_array($string))
        return stripslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_stripslashes($val);
    return $string;
}

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string) {
    if (!is_array($string))
        return addslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_addslashes($val);
    return $string;
}

/**
 * 生成SEO
 * @param $catid        栏目ID
 * @param $title        标题
 * @param $description  描述
 * @param $keyword      关键词
 */
function seo($catid = '', $title = '', $description = '', $keyword = '') {
    if (!empty($title))
        $title = strip_tags($title);
    if (!empty($description))
        $description = strip_tags($description);
    if (!empty($keyword))
        $keyword = str_replace(' ', ',', strip_tags($keyword));

    $site = F("Config");

    $categorys = F("Category");
    $cat = $categorys[$catid];
    $cat['setting'] = unserialize($cat['setting']);

    $seo['site_title'] = $site['sitename'];
    $titleKeywords="";
    $seo['keyword'] = $keyword!=$cat['setting']['meta_keywords']?(isset($keyword) && !empty($keyword) ? $keyword.(isset($cat['setting']['meta_keywords']) && !empty($cat['setting']['meta_keywords'])?",".$cat['setting']['meta_keywords']:""):$titleKeywords.(isset($cat['setting']['meta_keywords']) && !empty($cat['setting']['meta_keywords'])?",".$cat['setting']['meta_keywords']:"")):(isset($keyword) && !empty($keyword)?$keyword:$cat['catname']);
    $seo['description'] = isset($description) && !empty($description) ? $description : $title.(isset($keyword) && !empty($keyword)?$keyword:"");
    $seo['title'] = $cat['setting']['meta_title']!=$title?((isset($title) && !empty($title) ? $title . ' - ' : '') . (isset($cat['setting']['meta_title']) && !empty($cat['setting']['meta_title']) ? $cat['setting']['meta_title'] . ' - ' : (isset($cat['catname']) && !empty($cat['catname']) ? $cat['catname'] . ' - ' : ''))):(isset($title) && !empty($title)?$title." - ":($cat['catname']?$cat['catname']." - ":""));
    foreach ($seo as $k => $v) {
        $seo[$k] = str_replace(array("\n", "\r"), '', $v);
    }
    return $seo;
}


/**
 *  用于前台模板检测 
 * @param type $templateFile
 * @return boolean|string 
 */
function parseTemplateFile($templateFile = '') {
    static $TemplateFileCache = array();
    //模板路径
    $TemplatePath = TEMPLATE_PATH;
    //默认主题风格
    $ThemeDefault = "Default";
    //主题风格
    $Theme = empty(AppframeAction::$Cache["Config"]['theme']) ? $ThemeDefault : AppframeAction::$Cache["Config"]['theme'];
    //如果有指定 GROUP_MODULE 则模块名直接是GROUP_MODULE，否则使用 GROUP_NAME，这样做的目的是防止其他模块需要生成
    $group = defined('GROUP_MODULE') ? GROUP_MODULE . '/' : GROUP_NAME . '/';
    C('TEMPLATE_NAME', $TemplatePath . $Theme . "/" . $group . (THEME_NAME ? THEME_NAME . '/' : ''));
    //模板标识
    if ('' == $templateFile) {
        $templateFile = C('TEMPLATE_NAME') . MODULE_NAME . (defined('GROUP_NAME') ? C('TMPL_FILE_DEPR') : '/') . ACTION_NAME . C('TMPL_TEMPLATE_SUFFIX');
    }
    $key = md5($templateFile);
    if (isset($TemplateFileCache[$key])) {
        return $TemplateFileCache[$key];
    }
    if (false === strpos($templateFile, C('TMPL_TEMPLATE_SUFFIX'))) {
        // 解析规则为 模板主题:模块:操作 不支持 跨项目和跨分组调用
        $path = explode(':', $templateFile);
        $action = array_pop($path);
        $module = !empty($path) ? array_pop($path) : MODULE_NAME;

        if (!empty($path)) {// 设置模板主题
            $path = TEMPLATE_PATH . array_pop($path) . '/';
        } else {
            $path = C("TEMPLATE_NAME");
        }
        $depr = defined('GROUP_NAME') ? C('TMPL_FILE_DEPR') : '/';

        $templateFile = $path . $module . $depr . $action . C('TMPL_TEMPLATE_SUFFIX');
    }
    if (!file_exists_case($templateFile)) {
        //记录日志
        if (APP_DEBUG) {
            Log::write('模板:[' . $templateFile . ']不存在！');
        }
        //启用默认主题模板
        $templateFile = str_replace(C("TEMPLATE_NAME"), TEMPLATE_PATH . 'Default/' . $group, $templateFile);
        if (!file_exists_case($templateFile)) {
            $TemplateFileCache[$key] = false;
            return false;
        }
    }
    $TemplateFileCache[$key] = $templateFile;
    return $templateFile;
}

/**
 * 分页输出
 * @param type $Total_Size 总记录数
 * @param type $Page_Size 分页大小
 * @param type $Current_Page 当前页
 * @param type $listRows 显示页数
 * @param type $PageParam 分页参数
 * @param type $PageLink 分页链接
 * @param type $Static 是否(伪)静态
 * @return \Page 
 * 其他说明 ：当开启静态的时候 $PageLink 传入的是数组，数组格式
 * array(
  "index"=>"http://www.85zu.com/192.html",//这种是表示当前是首页，无需加分页1
  "list"=>"http://www.85zu.com/192-{page}.html",//这种表示分页非首页时启用
  )
 */
function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 1, $listRows = 6, $PageParam = '', $PageLink = '', $Static = FALSE, $TP = "") {
    static $_pageCache = array();
    $cacheIterateId = md5($Total_Size . $Page_Size . $Current_Page . $listRows . $PageParam);
    if (isset($_pageCache[$cacheIterateId]))
        return $_pageCache[$cacheIterateId];

    import('Page');
    if ($Page_Size == 0) {
        $Page_Size = C("PAGE_LISTROWS");
    }
    if (empty($PageParam)) {
        $PageParam = C("VAR_PAGE");
    }
    //生成静态，需要传递一个常量URLRULE，来生成对应规则
    if (empty($PageLink) && $Static == true) {
        $P = explode("~", URLRULE);
        $PageLink = array();
        $PageLink['index'] = $P[0];
        $PageLink['list'] = $P[1];
    }
    if (empty($TP)) {
        $TP = '共有{recordcount}条信息&nbsp;{pageindex}/{pagecount}&nbsp;{first}{prev}&nbsp;{liststart}{list}{listend}&nbsp;{next}{last}';
    }
    $Page = new Page($Total_Size, $Page_Size, $Current_Page, $listRows, $PageParam, $PageLink, $Static);
    $Page->SetPager('default', $TP, array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

    $_pageCache[$cacheIterateId];
    return $Page;
}

/**
 * 取得URL地址中域名部分
 * @param type $url 
 * @return \url 返回域名
 */
function urlDomain($url) {
    if (preg_match_all('#https?://(.*?)($|/)#m', $url, $Domain)) {
        return $Domain[0][0];
    }
    return false;
}

/**
 * 获取当前页面完整URL地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 *  通过用户邮箱，取得gravatar头像
 * @since 2.5
 * @param int|string|object $id_or_email 一个用户ID，电子邮件地址
 * @param int $size 头像图片的大小
 * @param string $default 如果没有可用的头像是使用默认图像的URL
 * @param string $alt 替代文字使用中的形象标记。默认为空白
 * @return string <img>
 */
function get_avatar($id_or_email, $size = '96', $default = '', $alt = false) {

    //头像大小
    if (!is_numeric($size))
        $size = '96';
    //邮箱地址
    $email = '';
    //如果是数字，表示使用会员头像 暂时没有写！
    if (is_int($id_or_email)) {
        $id = (int) $id_or_email;
        $userdata = service("Passport")->getLocalUser($id);
        $email = $userdata['email'];
    } else {
        $email = $id_or_email;
    }
    //设置默认头像
    if (empty($default)) {
        $default = 'mystery';
    }

    if (!empty($email))
        $email_hash = md5(strtolower($email));

    if (!empty($email))
        $host = sprintf("http://%d.gravatar.com", ( hexdec($email_hash[0]) % 2));
    else
        $host = 'http://0.gravatar.com';

    if ('mystery' == $default)
        $default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s={$size}"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
    elseif (!empty($email) && 'gravatar_default' == $default)
        $default = '';
    elseif ('gravatar_default' == $default)
        $default = "$host/avatar/s={$size}";
    elseif (empty($email))
        $default = "$host/avatar/?d=$default&amp;s={$size}";

    if (!empty($email)) {
        $out = "$host/avatar/";
        $out .= $email_hash;
        $out .= '?s=' . $size;
        $out .= '&amp;d=' . urlencode($default);

        $avatar = $out;
    } else {
        $avatar = $default;
    }

    return $avatar;
}

/**
 * 获取点击数
 * @param type $hitsid 如果是数组，则返回多个，如果是点击ID，则返回单条
 */
function hits($hitsid, $cache = 0) {
    if (is_array($hitsid)) {
        $cacheID = md5(implode(",", $hitsid));
    } else {
        $cacheID = md5($hitsid);
    }

    if ($cache && $data = S($cacheID)) {
        return $data;
    }
    $db = M("Hits");
    $where = array();
    if (is_array($hitsid)) {
        $where['hitsid'] = array("IN", implode(",", $hitsid));
        $data = $db->where($where)->getField("views");
    } else {
        $where['hitsid'] = array("EQ", $hitsid);
        $data = $db->where($where)->getField("views");
    }
    //缓存
    if ($cache) {
        S($cacheID, $data, $cache);
    }
    return $data;
}

/**
 * 标题链接获取
 * @param type $catid 栏目id
 * @param type $id 信息ID
 * @return type 链接地址
 */
function titleurl($catid, $id) {
    $Category = F("Category");
    $Model = F("Model");
    $tab = ucwords($Model[$Category[$catid]['modelid']]['tablename']);
    return M($tab)->where(array("id" => $id))->getField("url");
}

/**
 * 获取文章评论总数
 * @param type $catid 栏目ID
 * @param type $id 信息ID
 * @return type 
 */
function commcount($catid, $id) {
    $comment_id = "c-$catid-$id";
    return M("Comments")->where(array("comment_id" => $comment_id, "parent" => 0, "approved" => 1))->count();
}

/**
 * 把一维数组转化成1,2,3,4这样子的字符串
 * @param $arr
 */
function array1tostr($arr){
    if(empty($arr)){
        return '';
    }

    $str1 ='';

    foreach($arr as $_va){
        $str1 .= $_va.',';
    }

    $str1 = substr($str1,0,-1);
    return $str1;
}

/**
 * 把2维数组转化成1,2,3,4这样子的字符串
 * @param $arr
 */
function array2tostr($arr){
    if(empty($arr)){
        return '';
    }

    $str1 ='';

    foreach($arr as $oar){
        foreach($oar as $_va){
            $str1 .= $_va.',';
        }
    }

    $str1 = substr($str1,0,-1);
    return $str1;
}

/**
 * 对字符串转义和一些过滤处理
 * @param $str 字符串
 */
function gp($str,$type=''){

    $str = htmlspecialchars_decode($str);//把双引号、单引号由html转化成双引号、单引号

    if(empty($type)){
        if(get_magic_quotes_gpc()){
            $str = stripcslashes($str);//把双引号加上\
        }
    }
 return $str;
}

/**
 * 对字json符串转化为对象后，然后序列化通过URL传参数
 * @param $str 字符串
 * @param type为空为序列化,不空为转化成数组
 */
function ser($str,$type=''){
 return empty($type) ? base64_encode(serialize(json_decode($str))) : unserialize(base64_decode($str));
}

function show_form_select($value,$option,$selname,$selected='',$ext=''){
    $str = "<select name=\"" . $selname . "\" id=\"" . $selname . "\" " . $ext . ">\n";
    $i = 0;
    if(!is_array($value) || !is_array($option)) echo("数据初始化失败!");
    foreach($value as $a){
        $str .= "<option value=\"" . $a . "\"";
        if($selected!=''){
            if($a == $selected) $str .= " selected ";
        }
        $str .= ">" . $option[$i] . "</option>\n";
        $i++;
    }
    $str .= "</select>\n";
    return $str;
}

/**
 *type 为空就是menu,1为自定义字段设置
 *onlyname 不为空表示只取名称，而不是编号。
 *$parentid=''父节点id,$noparent=''是否需要父节点
 */
function showMenuParent0($selname,$selected = '',$ext = '',$defult = '',$type = '',$onlyname = '',$parentid='',$noparent=''){
    if(empty($type))
    {
        $dao = D('Menu');
        $sort = 'listorder asc';
    }else{
        if($type == 1){
            $dao = D('Customsetting');
            $sort = 'sort asc';
        }else{
            $dao = D($type);
            $sort = 'sort asc';
        }
    }
    //$dao = D('Menucfg');
    $finds = 'id,name';

    if(empty($noparent))
    {
        $condition['parentid'] = empty($parentid) ? 0 : $parentid;
    }

    $mlist = $dao->field($finds)->where($condition)->order($sort)->select();

    //print_r($mlist);

    if(empty($default)){
        $m_value[]="";
        $m_option[]=C('please select');
    }

    if(!empty($mlist)){
        foreach($mlist as $mone){
            $m_value[]= empty($onlyname) ? $mone['id'] :$mone['name'];
            $m_option[]=$mone['name'];
        }
    }

    return	$mstr = show_form_select($m_value,$m_option,$selname,$selected,$ext);



}

function nysts($ntext,$ytext,$finame,$default='',$fa,$ext){
    $qy_value[]="";
    $qy_option[]="请选择";

    $par=array(
        array('Y',$ytext),
        array('N',$ntext)
    );

    if($fa==1){
        $mrStr= "<option value=''>请选择</option>";
        if(is_array($par)){
            foreach($par as $stemp){
                $selc = $default == $stemp[0] ? 'selected' : '';

                $mrStr .= "<option value='" . $stemp[0] . "' $selc>" . $stemp[1] . " </option>";
            }
        }
    }else{

        if (is_array($par)){
            foreach ($par  as $qv){
                $qy_value[]=$qv[0];
                $qy_option[]=$qv[1];
            }
        }
        $_REQUEST[$finame] = $default == ''? $_REQUEST[$finame] : $default;
        $mrStr=show_form_select($qy_value,$qy_option,$finame,$_REQUEST[$finame],$ext);
    }

    return $mrStr;


}

?>
