<?php

$dataconfig = include 'dataconfig.php';
if (!is_array($dataconfig)) {
    $dataconfig = array();
}
$config = array(
    /* 项目设定 */
    'APP_STATUS' => 'debug', // 应用调试模式状态 调试模式开启后有效 默认为debug 可扩展 并自动加载对应的配置文件
    'APP_FILE_CASE' => true, // 是否检查文件的大小写 对Windows平台有效
    'APP_AUTOLOAD_PATH' => '@.TagLib', // 自动加载机制的自动搜索路径,注意搜索顺序
    'APP_TAGS_ON' => true, // 系统标签扩展开关
    /**
     * 提示（严重）：
     * 请不要修改此处，需要开启子域名部署，请在后台安装相应的域名绑定模块进行后台设置。
     */
    'APP_SUB_DOMAIN_DEPLOY' => false, // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES' => array(), 
    'APP_SUB_DOMAIN_DENY' => array(), //  子域名禁用列表
    'APP_GROUP_LIST' => 'Contents,Admin,Member', // 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
    'APP_GROUP_MODE' => 1, // 分组模式 0 普通分组 1 独立分组，本项目不允许使用普通分组
    'APP_GROUP_PATH' => 'Modules', // 分组目录 独立分组模式下面有效

    /* Cookie设置 */
    'COOKIE_EXPIRE' => 3600, // Coodie有效期
    'COOKIE_DOMAIN' => '', // Cookie有效域名
    'COOKIE_PATH' => '/', // Cookie路径
    'COOKIE_PREFIX' => 'zu_', // Cookie前缀 避免冲突

    /* SESSION设置 */
    'SESSION_AUTO_START' => true, // 是否自动开启Session
    'SESSION_OPTIONS' => array(), // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_TYPE' => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_PREFIX' => '', // session 前缀
    'VAR_SESSION_ID' => 'session_id', //sessionID的提交变量


    /* 默认设定 */
    'DEFAULT_APP' => '@', // 默认项目名称，@表示当前项目
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'DEFAULT_GROUP' => 'Admin', // 默认分组
    'DEFAULT_MODULE' => 'Public', // 默认模块名称
    'DEFAULT_ACTION' => 'login', // 默认操作名称
    'DEFAULT_CHARSET' => 'utf-8', // 默认输出编码
    'DEFAULT_TIMEZONE' => 'PRC', // 默认时区
    'DEFAULT_AJAX_RETURN' => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_FILTER' => 'htmlspecialchars', // 默认参数过滤方法 用于 $this->_get('变量名');$this->_post('变量名')...

    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => '', // 数据库名
    'DB_USER' => '', // 用户名
    'DB_PWD' => '', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'think_', // 数据库表前缀
    'DB_FIELDTYPE_CHECK' => true, // 是否进行字段类型检查
    'DB_FIELDS_CACHE' => true, // 启用字段缓存
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM' => 1, // 读写分离后 主服务器数量
    'DB_SQL_BUILD_CACHE' => false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE' => 'file', // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度

    /* 数据缓存设置 */
    'TMPL_CACHE_ON' => false,
    'DB_FIELD_CACHE' => false,
    'DATA_CACHE_TIME' => 0, // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS' => false, // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK' => false, // 数据缓存是否校验缓存
    'DATA_CACHE_TYPE' => 'File', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH' => TEMP_PATH, // 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR' => true, // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL' => 2, // 子目录缓存级别

    /* 错误设置 */
    'ERROR_MESSAGE' => '您浏览的页面暂时发生了错误！请稍后再试～', //错误显示信息,非调试模式有效
    'ERROR_PAGE' => '', // 错误定向页面
    'SHOW_ERROR_MSG' => false, // 显示错误信息

    /* 日志设置 */
    'LOG_RECORD' => true, // 默认不记录日志
    'LOG_TYPE' => 3, // 日志记录类型 0 系统 1 邮件 3 文件 4 SAPI 默认为文件方式
    'LOG_DEST' => '', // 日志记录目标
    'LOG_EXTRA' => '', // 日志记录额外信息
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR', // 允许记录的日志级别
    'LOG_FILE_SIZE' => 2097152, // 日志文件大小限制
    'LOG_EXCEPTION_RECORD' => false, // 是否记录异常信息日志

    /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE' => 'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR' => APP_PATH . 'Modules/Admin/Tpl/error.php', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => APP_PATH . 'Modules/Admin/Tpl/success.php', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => THINK_PATH . 'Tpl/think_exception.tpl', // 异常页面的模板文件
    'TMPL_FILE_DEPR' => '/',
    "DEFAULT_THEME" => "", //默认的模板主题名
    "TMPL_STRIP_SPACE" => false, //是否去除模板文件里面的html空格与换行
    'TMPL_TEMPLATE_SUFFIX' => '.php', //模板后缀

    /* URL设置 */
    'URL_CASE_INSENSITIVE' => false, // 默认false 表示URL区分大小写 true则表示不区分大小写
    /**
     * 水平凡提示：
     * 不建议修改全局配置的URL模式。
     * 如果会员中心需要相应模式，请在独立项目下增加Conf/config.php的方式配置。
     * 其他模块也是如此。
     * 内容模块（Contents）强烈不建议设置。
     */
    'URL_MODEL' => 0, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_DEPR' => '/', // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX' => '.html', // URL伪静态后缀设置

    /* 路由规则配置 */
    'URL_ROUTER_ON' => false, //是否开启路由
    'URL_ROUTE_RULES ' => array(),
    
    /* 系统变量名称设置  提示：请不要修改，否则出现未知问题 */
    'VAR_GROUP' => 'g', // 默认分组获取变量
    'VAR_MODULE' => 'm', // 默认模块获取变量
    'VAR_ACTION' => 'a', // 默认操作获取变量
    'VAR_AJAX_SUBMIT' => 'ajax', // 默认的AJAX提交变量
    'VAR_PATHINFO' => 's', // PATHINFO 兼容模式获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
    'VAR_URL_PARAMS' => '_URL_', // PATHINFO URL参数变量
    'VAR_FILTERS' => '', // 全局系统变量的默认过滤方法 多个用逗号分割

    /* 表单令牌 */
    'TOKEN_ON' => true, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true, //令牌验证出错后是否重置令牌 默认为true

    /* RBAC 提示：请不要修改。*/
    "USER_AUTH_ON" => true, //是否开启权限认证
    "USER_AUTH_TYPE" => 1, //默认认证类型 1 登录认证 2 实时认证
    "USER_AUTH_KEY" => "UserID", //用户认证SESSION标记，用于保存登陆后用户ID
    'ADMIN_AUTH_KEY' => 'administrator', //高级管理员无需进行权限认证$_SESSION['administrator']=true;
    "REQUIRE_AUTH_MODULE" => "", //需要认证模块
    "NOT_AUTH_MODULE" => "Public", //无需认证模块
    "USER_AUTH_GATEWAY" => "", //认证网关
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'USER_AUTH_MODEL' => 'User', //用户信息表

    /* 自定义配置 */
    "AUTHCODE" => "SfesEdg@#", //authcode加密函数密钥
    "UPLOAD_FILE_RULE" => "uniqid", //上传文件名命名规则 例如可以是 time uniqid com_create_guid 等 必须是一个无需任何参数的函数名 可以使用自定义函数
    "SHUIPF_FIELDS_PATH"=>LIB_PATH."Fields/",//字段地址
    "LODINGTEXT" =>'数据正在加载。。。',
    "NODATA"=>'没有找到你要的数据。。。',
    "edit"=>'修改',
    "please select" => '请选择',
    "separator"=>':',

    /* Interface 接口定义  */
    "INTERFACE_PASSPORT" => "Passport", //通行证服务

    /* 分页配置 */
    "PAGE_LISTROWS" => 20, //分页数
    "VAR_PAGE" => "page", //当前分页变量 page=2 page=3
    "UPLOADFILEPATH" => SITE_PATH . "/d/file/", //上传附件路径

    /* 标签库 */
    'TAGLIB_BUILD_IN' => 'cx,zu',
    
    /* 显示程序执行时间 */
    'SHOW_PAGE_TRACE' => false,
    'OUTPUT_ENCODE' => true, // 页面压缩输出

);

return array_merge($config, $dataconfig);
?>