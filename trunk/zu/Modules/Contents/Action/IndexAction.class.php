<?php

/**
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class IndexAction extends BaseAction {

    private $url;

    function _initialize() {
        parent::_initialize();
        import('Url');
        $this->url = new Url();
    }

    /**
     *首页 
     */
    public function index() {
        //模板
        $tp = explode(".", CONFIG_INDEXTP);
        //URL规则
        $urlrules = $this->url->index(0,"URLRULE");
        $urlrules = implode("~",$urlrules);
        define('URLRULE', $urlrules);
        //分页
        $page = isset($_GET[C("VAR_PAGE")]) ? $_GET[C("VAR_PAGE")] : 1;
        $template = parseTemplateFile("Index:" . $tp[0]);
        $SEO = seo();
        //seo分配到模板
        $this->assign("SEO", $SEO);
        //把分页分配到模板
        $this->assign(C("VAR_PAGE"), $page);
        $this->display($template);
    }

    /**
     * 栏目列表 
     */
    public function lists() {
        //栏目ID
        $catid = intval($_GET['catid']);
        if (!$catid) {
            $this->error("您没有访问该信息的权限！");
        }
        //全部栏目
        $CATEGORYS = F("Category");
        if (!isset($CATEGORYS[$catid])) {
            $this->error("该栏目不存在！");
        }
        //当前栏目信息
        $CAT = $CATEGORYS[$catid];
        extract($CAT);
        $setting = unserialize($setting);
        //SEO
        $SEO = seo($catid,  $setting['meta_title'], $setting['meta_description'], $setting['meta_keywords']);
        //分页
        $page = isset($_GET[C("VAR_PAGE")]) ? $_GET[C("VAR_PAGE")] : 1;
        //栏目首页模板
        $template = $setting['category_template'] ? $setting['category_template'] : 'category';
        //栏目列表模板
        $template_list = $setting['list_template'] ? $setting['list_template'] : 'list';
        //如果栏目类型是0则用栏目首页模板，栏目列表，则使用栏目列表模板
        $template = $child ? "Category:" . $template : "List:" . $template_list;
        //父栏目id
        $arrparentid = explode(',', $arrparentid);

        if ($CAT['create_to_html_root']) {
            $parentdir = '';
        }
        
        //取得URL规则
        $urls = $this->url->category_url($catid,$page,"URLRULE");
        $URLRULE = implode("~", $urls);
        define('URLRULE', $URLRULE);

        $this->assign("categorydir", $categorydir);
        $this->assign("catdir", $catdir);
        $this->assign($CAT);
        $this->assign("SEO", $SEO);
        $this->assign("page", $page);

        $template = explode(".", $template);
        $this->display($template[0]);
    }

    /**
     * 内容页 
     */
    public function shows() {
        $catid = $this->_get("catid");
        $id = $this->_get("id");
        $page = intval($_GET[C("VAR_PAGE")]);
        $page = max($page, 1);
        if (!$id || !$catid) {
            $this->error("缺少参数！");
        }
        $model = F("Model");
        $this->categorys = F("Category");
        //主表名称
        $tableName = ucwords($model[$this->categorys[$catid]['modelid']]['tablename']);
        if (empty($tableName)) {
            $this->error("模型不存在！");
        }

        $this->db = new ContentModel($tableName);
        $data = $this->db->relation(true)->where(array("id" => $id, 'status'=>99))->find();
        if(empty($data)){
            $this->error("该信息不存在！");
        }
        $data = array_merge($data, $data[$tableName . "_data"]);
        unset($data[$tableName . "_data"]);

        if (isset($data['paginationtype'])) {
            $paginationtype = $data['paginationtype'];
            $maxcharperpage = $data['maxcharperpage'];
        } else {
            $paginationtype = 0;
        }

        $catid = $data['catid'];
        $CATEGORYS = $this->categorys;
        $CAT = $CATEGORYS[$catid];

        //取得栏目配置
        $CAT['setting'] = unserialize($CAT['setting']);

        //模型ID
        $this->modelid = $modelid = $CAT['modelid'];
        
        //载入字段数据处理类
        require_cache(RUNTIME_PATH . 'content_output.class.php');
        $content_output = new content_output($modelid, $catid, $CATEGORYS);
        $output_data = $content_output->get($data);
        $output_data['id'] = $id;
        extract($output_data);

        //上一页
        $where = array();
        $where['catid'] = $catid;
        $where['status'] = array("EQ", "99");
        $where['id'] = array("LT", $id);
        $previous_page = $this->db->where($where)->order(array("id" => "DESC"))->find();
        //下一页
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
        //检测模板是否存在、不存在使用默认！
        $tempstatus = parseTemplateFile("Show:" . $template);
        if ($tempstatus == false) {
            $template = "show";
            unset($tempstatus);
        }
        
        //分页处理
        $pages = $titles = '';
        if ($paginationtype == 1) {
            //自动分页
            if ($maxcharperpage < 10) {
                $maxcharperpage = 500;
            }
            import('@.ORG.Contentpage');
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

        //文章支持分页
        if ( $paginationtype > 0) {
            $urlrules = $this->url->show($id, $page, $catid, $inputtime, $prefix,"","","URLRULE");
            $urlrules = implode("~", $urlrules);
            //print_r($urlrules);exit;
            define('URLRULE', $urlrules);
            //手动分页
            $CONTENT_POS = strpos($content, '[page]');
            if ($CONTENT_POS !== false) {
                $contents = array_filter(explode('[page]', $content));
                $pagenumber = count($contents);
                if (strpos($content, '[/page]') !== false && ($CONTENT_POS < 7)) {
                    $pagenumber--;
                }
                for ($i = 1; $i <= $pagenumber; $i++) {
                    $pageurls[$i] = $this->url->show($id, $i, $catid, $rs['inputtime']);
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
                //当不存在 [/page]时，则使用下面分页
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
            }
        }
        $this->assign("pages", $pages);
        $this->assign("content", $content);
        $this->display($template);
    }

}

?>
