<?php

/**
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class TagLibZu extends TagLib {

    /**
     * @var type 
     * 标签定义： 
     *                  attr         属性列表 
     *                  close      标签是否为闭合方式 （0闭合 1不闭合），默认为不闭合 
     *                  alias       标签别名 
     *                  level       标签的嵌套层次（只有不闭合的标签才有嵌套层次）
     * 定义了标签属性后，就需要定义每个标签的解析方法了，
     * 每个标签的解析方法在定义的时候需要添加“_”前缀，
     * 可以传入两个参数，属性字符串和内容字符串（针对非闭合标签）。
     * 必须通过return 返回标签的字符串解析输出，在标签解析类中可以调用模板类的实例。
     */
    protected $tags = array(
        //内容标签
        'content' => array('attr' => 'action,cache,num,page,return,pagetp,pagefun', 'level' => 3),
        //Tags标签
        'tags' => array('attr' => 'action,cache,num,page,return,pagetp,pagefun', 'level' => 3),
        //评论标签
        'comment' => array('attr' => 'action,cache,num,return', 'level' => 3),
        //友情链接标签
        'links' => array('attr' => 'action,cache,num,return', 'level' => 3),
        //推荐位标签
        'position' => array('attr' => 'action,cache,num,return', 'level' => 3),
        //SQL标签
        'get' => array("attr" => 'sql,cache,page,dbsource,return', 'level' => 3),
        //模板标签
        'template' => array("attr" => "file", "close" => 0),
        //后台模板标签
        'Admintemplate' => array("attr" => "file", "close" => 0),
        //Form标签
        'Form' => array("attr" => "function,parameter", "close" => 0),
    );

    /**
     * 模板包含标签 
     * 格式
     * <Admintemplate file="APP/模块/模板"/>
     * @staticvar array $_admintemplateParseCache
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     * @return array 
     */
    public function _Admintemplate($attr, $content) {
        static $_admintemplateParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_admintemplateParseCache[$cacheIterateId])) {
            return $_admintemplateParseCache[$cacheIterateId];
        }
        //分析Admintemplate标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'admintemplate');
        $file = explode("/", $tag['file']);
        $counts = count($file);
        if ($counts < 2) {
            return false;
        } else if ($counts < 3) {
            $file_path = DIRECTORY_SEPARATOR . "Admin" . DIRECTORY_SEPARATOR . "Tpl" . DIRECTORY_SEPARATOR . $tag['file'];
        } else {
            $file_path = DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . "Tpl" . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR . $file[2];
        }
        //模板路径
        $TemplatePath = APP_PATH . C("APP_GROUP_PATH") . $file_path . C("TMPL_TEMPLATE_SUFFIX");
        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            return false;
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板内容
        $parseStr = $this->tpl->parse($tmplContent);
        $_admintemplateParseCache[$cacheIterateId] = $parseStr;
        return $_admintemplateParseCache[$cacheIterateId];
    }

    /**
     * 模板中调用Form.class.php方法
     * 使用方法 <Form function="date" parameter="name,$valeu"/>
     * @param type $attr
     * @param type $content
     */
    public function _Form($attr, $content) {
        static $_FormParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_FormParseCache[$cacheIterateId])) {
            return $_FormParseCache[$cacheIterateId];
        }

        $tag = $this->parseXmlAttr($attr, 'form');
        $function = $tag['function'];
        if (!$function) {
            return false;
        }

        $parameter = explode(",", $tag['parameter']);
        foreach ($parameter as $k => $v) {
            if ($v == "''" || $v == '""') {
                $v = "";
            }
            $parameter[$k] = trim($v);
        }
        $parameter = $this->arr_to_html($parameter);

        $parseStr = "<?php ";
        $parseStr .= " import(\"Form\");";
        $parseStr .= ' echo call_user_func_array(array("Form","' . $function . '"),' . $parameter . ')';
        //$parseStr .= " echo Form::$function(".$tag['parameter'].");\r\n";
        $parseStr .= " ?>";

        $_FormParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 模板包含标签 
     * 格式
     * <template file="Member/footer.php"/>
     * @staticvar array $_templateParseCache
     * @param type $attr 属性字符串
     * @param type $content 标签内容
     * @return array 
     */
    public function _template($attr, $content) {
        static $_templateParseCache = array();
        $cacheIterateId = md5($attr . $content);
        if (isset($_templateParseCache[$cacheIterateId])) {
            return $_templateParseCache[$cacheIterateId];
        }
        //检查CONFIG_THEME是否被定义
        if (!defined("CONFIG_THEME")) {
            return;
        }
        //分析template标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'template');
        $TemplatePath = TEMPLATE_PATH . CONFIG_THEME . DIRECTORY_SEPARATOR . $tag['file'];
        //判断模板是否存在
        if (!file_exists_case($TemplatePath)) {
            //启用默认模板
            $TemplatePath = TEMPLATE_PATH . "Default" . DIRECTORY_SEPARATOR . $tag['file'];
            if (!file_exists_case($TemplatePath)) {
                return;
            }
        }
        //读取内容
        $tmplContent = file_get_contents($TemplatePath);
        //解析模板
        $parseStr = $this->tpl->parse($tmplContent);
        $_templateParseCache[$cacheIterateId] = $parseStr;
        return $_templateParseCache[$cacheIterateId];
    }

    /**
     * 内容标签
      +----------------------------------------------------------
     * 公共参数
     * 参数名	 是否必须	 默认值	 说明
     * cache    否                0                   数据缓存时间，单位秒
     * pagefun 否              page             分页全局函数
     * pagetp  否               默认分页模板   分页模板
     * return   否                $data           返回值变量名称
     * ----------------------------------------------------------
     * 
     * lists 内容列表
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID
     * where	 否	 null	 sql语句的where部分 例如：thumb`!='' AND `status`=99
     * thumb	 否	 0	 是否仅必须缩略图 例如：id DESC
     * order	 否	 null	 排序类型
     * num	 是	 null	 数据调用数量
     * moreinfo	 否	 0	 是否调用副表数据
     * 
     * moreinfo参数属性，本参数表示在返回数据的时候，会把副表中的数据也一起返回。一个内容模型分为2个表，一个主表一个副表，主表中一般是保存了标题、所属栏目等等短小的数据（方便用于索引），而副表则保存了大字段的数据，如内容等数据。在模型管理中新建字段的时候，是允许你选择存入到主表还是副表的（我们推荐的是，把不重要的信息放到副表中）。
     * 
     * 格式：
     * <content action="lists" catid="$catid"  order="id DESC" num="4" page="$page">
     *  
     * </content>
     * 
     * hits 排行榜标签
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID
     * day	 否	 0	 调用多少天内的排行
     * order	 否	 null	 排序类型（本月排行- monthviews DESC 、本周排行 - weekviews DESC、今日排行 - dayviews DESC）
     * num	 是	 null	 数据调用数量
     * 
     *  格式：
     * <content action="hits" catid="$catid"  order="weekviews DESC" num="10">
     *  
     * </content>
     * 
     * relation 相关文章标签
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID
     * relation	 否	 $relation	 无需更改
     * nid	 否	 null	 排除id 一般是 $id，排除当前文章
     * order	 否	 null	 排序
     * keywords	 否	 null	 内容页面取值：$keywords
     * num	 是	 null	 数据调用数量
     * 
     * 格式：
     * <content action="relation" relation="$relation" catid="$catid"  order="id DESC" num="5" keywords="$keywords">
     *  
     * </content>
     * 
     * category 栏目列表
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 0	 调用该栏目下的所有栏目 ，默认0，调用一级栏目
     * order	 否	 null	 排序方式、一般按照listorder ASC排序，即栏目的添加顺序
     * 
     * 格式：
     * <content action="category" catid="$catid"  order="listorder ASC" >
     *  
     * </content>
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
      +----------------------------------------------------------
     * @return string|void
      +----------------------------------------------------------
     */
    public function _content($attr, $content) {
        static $_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        //分析content标签的标签定义
        $tag = $this->parseXmlAttr($attr, 'content');
        /* 属性列表 */
        $num = (int) $tag['num']; //每页显示总数
        $page = (int) $tag['page']; //当前分页
        $pagefun = empty($tag['pagefun']) ? "page" : $tag['pagefun']; //分页函数，默认page
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法
        $pagetp = $tag['pagetp']; //分页模板

        $parseStr = '<?php';
        $parseStr .= ' $content_tag = TagLib("Content");';
        //如果有传入$page参数，则启用分页。
        if (isset($page)) {
            $tag = array_merge($tag, array(
                "count" => '$count',
                "limit" => '$_page_->firstRow.",".$_page_->listRows'
            ));
            $parseStr .= ' $count = $content_tag->count(' . self::arr_to_html($tag) . ');';
            $parseStr .= ' $_GET[C("VAR_PAGE")] = $page;';
            $parseStr .= ' $pagetp = "' . $pagetp . '";';
            $parseStr .= ' $_page_ = ' . $pagefun . '($count ,' . $num . ',$page,6,C("VAR_PAGE"),"",true,$pagetp);';
            //使用常量来保存总分页数，有个缺陷，如果不使用跳转的方式生成，会出现生成下一个栏目的时候无法对该常量进行重新赋值，使用跳转生成下一个栏目则没有这个问题！
            $parseStr .= ' if (!defined("PAGES")){define("PAGES", $_page_->Total_Pages);}';
            //_PAGES_ 用于解决上述所说的问题，这样可以根据$_GET["_PAGES_"] 来判断要生成多少页了
            $parseStr .= ' $_GET["_PAGES_"] = $_page_->Total_Pages;';
            $parseStr .= ' $pages = $_page_->show("default");';
            $parseStr .= ' $pagesize = ' . $num . ';';
            $parseStr .= ' $offset = ($page - 1) * $pagesize;';
        }
        $parseStr .= ' if(method_exists($content_tag, "' . $action . '")){';
        $parseStr .= ' $' . $return . ' = $content_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' }';

        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 评论标签
     *  +----------------------------------------------------------
     * 公共参数
     * 参数名	 是否必须	 默认值	 说明
     * cache    否                0                   数据缓存时间，单位秒
     * return   否                $data           返回值变量名称
     * +----------------------------------------------------------
     * get_comment 获取评论总数
     * 参数名	 是否必须	 默认值	 说明
     * catid	 是	 null	 栏目ID
     * id	 是	 null	 信息ID
     * 格式：
     * <comment action="get_comment" catid="$catid" id="$id">
     * {$data.total}
     * </comment>
     * return array() commentid|total
     * 
     * lists  评论数据列表
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 栏目ID
     * id	 否	 null	 信息ID
     * hot	 否	 0	 排序方式｛0：最新｝ 
     * date	 否	 Y-m-d H:i:s A	时间格式
     * 格式：
     * <comment action="lists" catid="$catid" id="$id">
     * {$data.total}
     * </comment>
     * 
     * bang 评论排行榜 （暂时没有实现）
     * 格式：
     * <comment action="bang" catid="$catid" id="$id">
     * {$data.total}
     * </comment>
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _comment($attr, $content) {
        static $_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'comment');
        /* 属性列表 */
        $num = (int) $tag['num']; //每页显示总数
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法

        $parseStr = '<?php';
        $parseStr .= ' $comment_tag = TagLib("Comment");';
        $parseStr .= ' if(method_exists($comment_tag, "' . $action . '")){';
        $parseStr .= ' $' . $return . ' = $comment_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' }';
        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * Tags标签
     *  +----------------------------------------------------------
     * list 列表
     * 参数名	 是否必须	 默认值	 说明
     * tag	 否	 null	 tag名称  注意：tag和tagid不可以同时存在  $tag
     * tagid	 否	 null	 tagID   注意：tag和tagid不可以同时存在 $tagid
     * num	 否	 10	 返回数量
     * order	 否	 null	 排序类型
     * 格式：
     * <tags action="lists" tag="$tag" num="4" page="$page" order="id DESC">
     * 
     * </tags>
     * 
     * top Tag排行榜
     * 参数名	 是否必须	 默认值	 说明
     * num	 否	 10	 返回数量
     * order	 否	 hits DESC	 排序类型
     * 
     * <tags action="top"  num="4"  order="id DESC">
     * 
     * </tags>
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _tags($attr, $content) {
        static $_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'tags');
        /* 属性列表 */
        $num = (int) $tag['num']; //每页显示总数
        $page = $tag['page']; //当前分页
        $pagefun = empty($tag['pagefun']) ? "page" : $tag['pagefun']; //分页函数，默认page
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法
        $pagetp = $tag['pagetp']; //分页模板

        $parseStr = '<?php';
        $parseStr .= ' $Tags_tag = TagLib("Tags");';
        //如果有传入$page参数，则启用分页。
        if (isset($page)) {
            $tag = array_merge($tag, array(
                "count" => '$count',
                'limit' => '$_page_->firstRow.",".$_page_->listRows'
            ));
            $parseStr .= ' $count = $Tags_tag->count(' . self::arr_to_html($tag) . ');';
            $parseStr .= ' $_GET[C("VAR_PAGE")] = $page;';
            $parseStr .= ' $pagetp = "' . $pagetp . '";';
            $parseStr .= ' $_page_ = ' . $pagefun . '($count ,' . $num . ',$page,6,C("VAR_PAGE"),"",true,$pagetp);';
            //使用常量来保存总分页数，有个缺陷，如果不使用跳转的方式生成，会出现生成下一个栏目的时候无法对该常量进行重新赋值，使用跳转生成下一个栏目则没有这个问题！
            $parseStr .= ' if (!defined("PAGES")){define("PAGES", $_page_->Total_Pages);}';
            //_PAGES_ 用于解决上述所说的问题，这样可以根据$_GET["_PAGES_"] 来判断要生成多少页了
            $parseStr .= ' $_GET["_PAGES_"] = $_page_->Total_Pages;';
            $parseStr .= ' $pages = $_page_->show("default");';
            $parseStr .= ' $pagesize = ' . $num . ';';
            $parseStr .= ' $offset = ($page - 1) * $pagesize;';
        }
        $parseStr .= ' if(method_exists($Tags_tag, "' . $action . '")){';
        $parseStr .= '     $' . $return . ' = $Tags_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' };';

        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 友情链接标签
     * 
     *  +----------------------------------------------------------
     * 公共参数
     * 参数名	 是否必须	 默认值	 说明
     * cache	否 	 0	 缓存时间，单位为秒 
     * 
     * type_list 获取友情链接列表
     * 
     * 参数名	 是否必须	 默认值	 说明
     * order	 是	 id DESC	 排序方式
     * termsid	 否	 null	 分类ID
     * id	 否	 null	 链接ID 
     * 格式：
     * <links action="type_list" termsid="1" id="1">
     * {$data.total}
     * </links>
     * 
     * type_list
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _links($attr, $content) {
        static $_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'links');
        /* 属性列表 */
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法

        $parseStr = '<?php';
        $parseStr .= ' $links_tag = TagLib("Links");';
        $parseStr .= ' if(method_exists($links_tag, "' . $action . '")){';
        $parseStr .= '     $' . $return . ' = $links_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' };';
        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * 推荐位标签
     * 
     *  +----------------------------------------------------------
     * position 获取推荐位
     * 参数	    必须	     默认值	    说明
     * posid	 是	 null	 推荐位ID
     * catid	 否	 null	 调用栏目ID
     * thumb	 否	 0	 是否仅必须缩略图
     * order	 否	 null	 排序类型
     * num	 是	 null	 数据调用数量
     * cache	 否	 0	 缓存时间，单位为秒 
     * 格式：
     * <position action="position" posid="1">
     *  
     * </position>
     * 
     * position
      +----------------------------------------------------------
     * @param string $attr 标签属性
     * @param string $content  标签内容
     */
    public function _position($attr, $content) {
        static $_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'position');
        /* 属性列表 */
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $action = $tag['action']; //方法

        $parseStr = '<?php';
        $parseStr .= ' $Position_tag = TagLib("Position");';
        $parseStr .= ' if(method_exists($Position_tag, "' . $action . '")){';
        $parseStr .= '     $' . $return . ' = $Position_tag->' . $action . '(' . self::arr_to_html($tag) . ');';
        $parseStr .= ' };';
        $parseStr .= ' ?>';
        $parseStr .= $this->tpl->parse($content);
        $_iterateParseCache[$cacheIterateId] = $parseStr;
        return $parseStr;
    }

    /**
     * get标签
     * 参数	 默认值	 必须	 说明
     * sql          null	 是	 要执行的SQL语句，只支持select类型
     * page	 0	 否	 分页，通过变量把当前的分布传给P标签进行处理 $page
     * pagefun null            否                 分页函数，传入的固定参数请参考系统自带分页方法 page，在common.php文件
     * pagetp null              否                 分页模板
     * num    20                  否                每页显示数量
     * return    data            否                返回变量名
     * dbsource	 null	 否	 数据源，当你通过系统后台的数据源模块配置过数据源时，可把数据源名填写到这里，系统会去对应的数据源来读取数据。如果要读取本系统的数据请留空（本功能暂时没有实现）
     * 
     * return	 data	 否	  返回的数据的变量
     * 
     * +----------------------------------------------------------
     * 范例：
     * <get sql="SELECT * FROM think_article  WHERE status=99 ORDER BY inputtime DESC" page="$page" num="5">
     *    ....其余模板代码...
     * </get>
     * +----------------------------------------------------------
     * @param type $attr
     * @param type $content 
     */
    public function _get($attr, $content) {
        $tag = $this->parseXmlAttr($attr, 'get');
        $sql = $tag['sql'];
        $page = $tag['page']; //当前分页
        $cache = (int) $tag['cache'];
        $pagefun = empty($tag['pagefun']) ? "page" : $tag['pagefun']; //分页函数，默认page
        $pagetp = $tag['pagetp']; //分页模板
        $num = isset($tag['num']) && intval($tag['num']) > 0 ? intval($tag['num']) : 20; //每页显示总数
        $return = empty($tag['return']) ? "data" : $tag['return']; //数据返回变量
        $tag['sql'] = str_replace(array("think_", "shuipfcms_"), C("DB_PREFIX"), strtolower($tag['sql']));

        //删除，插入不执行！这样处理感觉有点鲁莽了，，，-__,-!
        if (strpos($tag['sql'], "delete") || strpos($tag['sql'], "insert")) {
            return;
        }

        $str = ' <?php ';
        //如果配置了数据源
        if ($datas['dbsource']) {
            
        } else {
            $str .= ' $get_db = M();';
        }
        //有启用分页
        if (isset($page)) {
            //分析SQL语句
            if ($sql = preg_replace('/select([^from].*)from/i', "SELECT COUNT(*) as count FROM ", $tag['sql'])) {
                $str .= ' if(' . $cache . ' && $data = S( md5("' . $tag['sql'] . $cache . '".$page) ) ){ ';
                $str .= ' $pagetp = "' . $pagetp . '";';
                $str .= ' $_page_ = ' . $pagefun . '($data["count"] ,' . $num . ',$page,6,C("VAR_PAGE"),"",true,$pagetp);';
                $str .= ' $_GET["_PAGES_"] = $_page_->Total_Pages;';
                $str .= ' $pages = $_page_->show("default");';
                $str .= ' $pagesize = ' . $num . ';';
                $str .= ' $offset = ($page - 1) * $pagesize;';
                $str .= ' $' . $return . '= $data["data"];';
                $str .= ' }else{ ';
                //取得信息总数
                $str .= ' $count = $get_db->query("' . $sql . '");';
                $str .= ' $count = $count[0]["count"]; ';
                $str .= ' $_GET[C("VAR_PAGE")] = $page;';
                //分页模板
                $str .= ' $pagetp = "' . $pagetp . '";';
                $str .= ' $_page_ = ' . $pagefun . '($count ,' . $num . ',$page,6,C("VAR_PAGE"),"",true,$pagetp);';
                //使用常量来保存总分页数，有个缺陷，如果不使用跳转的方式生成，会出现生成下一个栏目的时候无法对该常量进行重新赋值，使用跳转生成下一个栏目则没有这个问题！
                $str .= ' if (!defined("PAGES")){define("PAGES", $_page_->Total_Pages);}';
                //_PAGES_ 用于解决上述所说的问题，这样可以根据$_GET["_PAGES_"] 来判断要生成多少页了
                $str .= ' $_GET["_PAGES_"] = $_page_->Total_Pages;';
                $str .= ' $pages = $_page_->show("default");';
                $str .= ' $pagesize = ' . $num . ';';
                $str .= ' $offset = ($page - 1) * $pagesize;';
                $str .= ' $' . $return . '=$get_db->query("' . $tag['sql'] . ' LIMIT ".$_page_->firstRow.",".$_page_->listRows." ");';
                $str .= ' if(' . $cache . '){ S( md5("' . $tag['sql'] . $cache . '".$page)  ,array("count"=>$count,"data"=>$' . $return . '),' . $cache . '); }; ';
                $str .= ' } ';
            }
        } else {
            $str .= ' if(' . $cache . ' && $data = S( md5("' . $tag['sql'] . $cache . '") ) ){ ';
            $str .= ' $' . $return . '=$data;';
            $str .= ' }else{ ';
            $str .= ' $' . $return . '=$get_db->query("' . $tag['sql'] . ' LIMIT ' . $num . ' ");';
            $str .= ' if(' . $cache . '){ S( md5("' . $tag['sql'] . $cache . '")  ,$' . $return . ',' . $cache . '); }; ';
            $str .= ' } ';
        }
        $str .= '  ?>';
        $str .= $this->tpl->parse($content);
        return $str;
    }

    /**
     * 转换数据为HTML代码
     * @param array $data 数组
     */
    private static function arr_to_html($data) {
        if (is_array($data)) {
            $str = 'array(';
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $str .= "'$key'=>" . self::arr_to_html($val) . ",";
                } else {
                    if (strpos($val, '$') === 0) {
                        $str .= "'$key'=>$val,";
                    } else {
                        $str .= "'$key'=>'" . new_addslashes($val) . "',";
                    }
                }
            }
            return $str . ')';
        }
        return false;
    }

}

?>
