<?php

/**
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class Html extends BaseAction {

    private $url, $categorys;

    public function _initialize() {
        //关闭由于启用域名绑定造成的前台域名出错
        define("APP_SUB_DOMAIN_NO", 1);
        parent::_initialize();
        import('Url');
        //栏目缓存
        $this->categorys = F("Category");
        $this->url = new Url();
        $this->Model = F("Model");
        define('HTML', true);
        C('HTML_FILE_SUFFIX', "");
    }

    /**
     * 生成内容页
     * @param  $file 文件地址
     * @param  $data 数据
     * @param  $array_merge 是否合并
     * @param  $action 方法
     */
    public function show($file, $data = '', $array_merge = 1, $action = 'add') {
        //取得信息ID
        $id = $data['id'];
        //判断数据是否已经合并成一个数组，而不是分主表和附表
        if ($array_merge) {
            $data = new_stripslashes($data);
            $data = array_merge($data['system'], $data['model']);
            $data['id'] = $id;
        }
        //通过rs获取原始值
        $rs = $data;
        //栏目ID
        $catid = $data['catid'];
        //获取栏目缓存
        $Category = $this->categorys;
        //获取当前栏目数据
        $CAT = $Category[$catid];
        //反序列化栏目配置
        $CAT['setting'] = unserialize($CAT['setting']);
        //模型ID
        $this->modelid = $modelid = $CAT['modelid'];
        //获取主表名
        $this->table_name = $this->Model[$this->modelid]['tablename'];
        //处理由于通过关联模型获取数据，会把副表字段内容归入下标为 表名_data ，重新组合
        if (isset($data[ucwords($this->table_name) . "_data"])) {
            $data = array_merge($data, $data[ucwords($this->table_name) . "_data"]);
            unset($data[ucwords($this->table_name) . "_data"]);
        }
        //分页方式
        if (isset($data['paginationtype'])) {
            //分页方式 
            $paginationtype = $data['paginationtype'];
            //自动分页字符数
            $maxcharperpage = (int) $data['maxcharperpage'];
        } else {
            //默认不分页
            $paginationtype = 0;
        }

        //载入字段数据处理类
        if (!file_exists(RUNTIME_PATH . 'content_output.class.php')) {
            $this->error("请更新缓存后再操作！");
        }
        require_cache(RUNTIME_PATH . 'content_output.class.php');
        $content_output = new content_output($modelid, $catid, $Category);
        //获取字段类型处理以后的数据
        $output_data = $content_output->get($data);
        $output_data['id'] = $id;
        extract($output_data);

        $this->table_name = $this->Model[$this->modelid]['tablename'];
        $this->db = M(ucwords($this->table_name));

        //上一篇
        $where = array();
        $where['catid'] = $catid;
        $where['status'] = array("EQ", "99");
        $where['id'] = array("LT", $id);
        $previous_page = $this->db->where($where)->order(array("id" => "DESC"))->find();
        //下一篇
        $where['id'] = array("GT", $id);
        $next_page = $this->db->where($where)->find();

        if (empty($previous_page)) {
            $previous_page = array('title' => "第一页", 'thumb' => CONFIG_SITEURL . 'statics/images/nopic_small.gif', 'url' => 'javascript:alert(\'第一页\');');
        }
        if (empty($next_page)) {
            $next_page = array('title' => "最后一页", 'thumb' => CONFIG_SITEURL . 'statics/images/nopic_small.gif', 'url' => 'javascript:alert(\'最后一页\');');
        }

        $output_data['title'] = $title = strip_tags($title);
        //SEO
        $seo_keywords = '';
        if (!empty($keywords)) {
            $seo_keywords = implode(',', $keywords);
        }
        $SEO = seo($catid, $title, $description, $seo_keywords);

        //模板处理开始
        $template = $template ? $template : $CAT['setting']['show_template'];
        //去除模板文件后缀
        $newstempid = explode(".", $template);
        $template = $newstempid[0];
        unset($newstempid);
        //检测模板是否存在、不存在使用默认！
        $tempstatus = parseTemplateFile("Show:" . $template);
        if ($tempstatus == false) {
            //模板不存在，重新使用默认模板
            $template = "show";
            $tempstatus = parseTemplateFile("Show:" . $template);
        }

        //分页处理
        $pages = $titles = '';
        if ($paginationtype == 1) {
            //自动分页
            if ($maxcharperpage < 10) {
                $maxcharperpage = 500;
            }
            //按字数分割成几页处理开始
            import('Contentpage', APP_PATH . C("APP_GROUP_PATH") . '/Contents/ORG');
            $contentpage = new Contentpage();
            $contentfy = $contentpage->get_data($content, $maxcharperpage);
            //自动分页有时会造成返回空，如果返回空，就不分页了
            if (!empty($contentfy)) {
                $content = $contentfy;
            }
        }

        //分配变量到模板 
        $this->assign($output_data);
        //seo分配到模板
        $this->assign("SEO", $SEO);
        //上一篇 下一篇
        $this->assign("previous_page", $previous_page);
        $this->assign("next_page", $next_page);
        //栏目ID
        $this->assign("catid", $catid);

        //模板地址
        $template = $tempstatus;
        //分页生成处理
        if ($paginationtype > 0) {
            //手动分页
            $CONTENT_POS = strpos($content, '[page]');
            if ($CONTENT_POS !== false) {
                $contents = array_filter(explode('[page]', $content));
                $pagenumber = count($contents);
                if (strpos($content, '[/page]') !== false && ($CONTENT_POS < 7)) {
                    $pagenumber--;
                }
                for ($i = 1; $i <= $pagenumber; $i++) {
                    $pageurls[$i] = $this->url->show($id, $i, $catid, $data['inputtime'], $data['prefix'], '', 'edit');
                }
                $END_POS = strpos($content, '[/page]');
                if ($END_POS !== false) {
                    if ($CONTENT_POS > 7) {
                        $content = '[page]' . $title . '[/page]' . $content;
                    }
                    if (preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
                        foreach ($m[1] as $k => $v) {
                            $p = $k + 1;
                            $titles[$p]['title'] = strip_tags($v);
                            $titles[$p]['url'] = $pageurls[$p][0];
                        }
                    }
                }

                $urlrules = $this->url->show($id, $page, $catid, $data['inputtime'], $data['prefix'], "", "", "URLRULE");
                $urlrules = implode("~", $urlrules);
                define('URLRULE', $urlrules);
                $pages = "";
                //生成分页
                foreach ($pageurls as $page => $urls) {
                    //$pagenumber 分页总数
                    $_GET[C("VAR_PAGE")] = $page;
                    $pages = page($pagenumber, 1, $page, 6, C("VAR_PAGE"), '', true)->show("default");
                    //判断[page]出现的位置是否在第一位 
                    if ($CONTENT_POS < 7) {
                        $content = $contents[$page];
                    } else {
                        if ($page == 1 && !empty($titles)) {
                            $content = $title . '[/page]' . $contents[$page - 1];
                        } else {
                            $content = $contents[$page - 1];
                        }
                    }
                    if ($titles) {
                        list($title, $content) = explode('[/page]', $content);
                        $content = trim($content);
                        if (strpos($content, '</p>') === 0) {
                            $content = '<p>' . $content;
                        }
                        if (stripos($content, '<p>') === 0) {
                            $content = $content . '</p>';
                        }
                    }

                    //分页
                    $this->assign("pages", $pages);
                    $this->assign("content", $content);
                    $pagefile = $urls[1]; //生成路径
                    $this->buildHtml($pagefile, SITE_PATH . "/", $template);
                }
                $this->assign("pages", "");
                return true;
            }
        }
        $this->assign("content", $content);
        //分页处理结束
        $filename = $file; //生成路径
        $this->buildHtml($filename, SITE_PATH . "/", $template);
        return true;
    }

    /**
     * 根据页码生成栏目
     * @param $catid 栏目id
     * @param $page 当前页数
     */
    public function category($catid, $page = 1) {
        //把分页分配到模板
        $this->assign(C("VAR_PAGE"), $page);
        //获取栏目数据
        $CAT = $this->categorys[$catid];
        if (empty($CAT)) {
            $this->error("栏目不存在");
        }
        @extract($CAT);
        //是否生成列表
        if (!$ishtml) {
            return false;
        }
        //栏目扩展配置
        $setting = unserialize($setting);
        //SEO
        $SEO = seo($catid, $setting['meta_title'], $setting['meta_description'], $setting['meta_keywords']);
        //分页
        $page = intval($page);
        //父目录
        $parentdir = $CAT['parentdir'];
        //目录
        $catdir = $CAT['catdir'];
        //生成路径
        $buildHtml_Urls = $this->url->category_url($catid, $page);
        $filename = $buildHtml_Urls[1];
        //取得URL规则
        $urls = $this->url->category_url($catid, $page, "URLRULE");

        //生成类型为0的栏目
        if ($type == 0) {
            //栏目首页模板
            $template = $setting['category_template'] ? $setting['category_template'] : 'category';
            //栏目列表页模板
            $template_list = $setting['list_template'] ? $setting['list_template'] : 'list';
            //判断使用模板类型，如果有子栏目使用频道页模板，终极栏目使用的是列表模板
            $template = $child ? "Category:" . $template : "List:" . $template_list;
            //去除后缀开始
            $tpar = explode(".", $template);
            //去除完后缀的模板
            $template = $tpar[0];
            unset($tpar);
            //模板检测
            $template = parseTemplateFile($template);
            define('URLRULE', implode("~", $urls));
        }
        //分配变量到模板 
        $this->assign($CAT);
        //seo分配到模板
        $this->assign("SEO", $SEO);
        //生成
        $this->buildHtml($filename, SITE_PATH . "/", $template);
    }

    /**
     * 生成栏目列表
     * @param $catid 栏目id
     */
    public function HtmlCategory($catid) {
        $page = 1;
        $j = 1;
        //开始生成列表
        do {
            $this->category($catid, $page);
            $page++;
            $j++;
            //如果GET有total_number参数则直接使用GET的，如果没有则根据常量 PAGES获取分页总数
            $total_number = isset($total_number) ? $total_number : PAGES;
        } while ($j <= $total_number);

        return true;
    }

    /**
     * 更新首页
     * @param $page 页码，默认1
     */
    public function index($page = 1) {
        $page = max($page, 1);
        if (CONFIG_GENERATE == '0' || CONFIG_GENERATE < 1) {
            return false;
        }

        //模板处理
        $tp = explode(".", CONFIG_INDEXTP);
        $template = parseTemplateFile("Index:" . $tp[0]);
        if ($template == false) {
            $this->error("首页模板不存在！");
        }

        //URL规则
        $urlrules = $this->url->index(0, "URLRULE");
        $urlrules = implode("~",$urlrules);
        define('URLRULE', $urlrules);

        $SEO = seo();
        
        $j = 1;
        //分页生成
        do {
            //生成路径
            $urls = $this->url->index($page);
            $filename = $urls[1];
            
            //把分页分配到模板
            $this->assign(C("VAR_PAGE"), $page);
            //seo分配到模板
            $this->assign("SEO", $SEO);
            //判断是否生成和入口文件同名，如果是，不生成！
            if($filename != "/index.php"){
                $this->buildHtml($filename, SITE_PATH . "/", $template);
            }
            
            $page++;
            $j++;
            //如果GET有total_number参数则直接使用GET的，如果没有则根据常量 PAGES获取分页总数
            $total_number = isset($total_number) ? $total_number : PAGES;
        } while ($j <= $total_number);
    }

    /**
     * 生成相关栏目列表、只生成前5页
     * @param $catid
     */
    public function create_relation_html($catid) {
        $page = 1;
        $j = 1;
        //开始生成列表
        do {
            $this->category($catid, $page);
            $page++;
            $j++;
            //如果GET有total_number参数则直接使用GET的，如果没有则根据常量 PAGES获取分页总数
            $total_number = isset($total_number) ? $total_number : PAGES;
        } while ($j <= $total_number && $j < 7);
        //检查当前栏目的父栏目，如果存在则生成
        $arrparentid = $this->categorys[$catid]['arrparentid'];
        if ($arrparentid) {
            $arrparentid = explode(',', $arrparentid);
            foreach ($arrparentid as $catid) {
                if ($catid)
                    $this->category($catid, 1);
            }
        }
    }

    /**
     * 生成自定义页面 
     * @param $temptext 模板内容
     * @param $data 数据
     */
    public function createhtml($temptext, $data) {
        if (!$temptext || !is_array($data)) {
            return false;
        }
        //生成文件名，包含后缀
        $filename = $data['tempname'];
        //生成路径
        $htmlpath = SITE_PATH . $data['temppath'] . $filename;
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        parent::show($temptext);
        // 获取并清空缓存
        $content = ob_get_clean();
        //检查目录是否存在
        if (!is_dir(dirname($htmlpath))) {
            // 如果静态目录不存在 则创建
            mkdir(dirname($htmlpath), 0777, true);
        }
        //写入文件
        if (false === file_put_contents($htmlpath, $content)) {
            throw_exception("自定义页面生成失败：" . $htmlpath);
        }
        return true;
    }

}