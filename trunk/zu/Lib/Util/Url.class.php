<?php

/**
 * URL处理类
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
define("SYS_TIME", time()); //时间

class Url {

    private $urlrules, $categorys;

    function __construct() {
        //栏目缓存
        $this->categorys = F("Category");
        if(!$this->categorys){
            D("Category")->category_cache();
            $this->categorys = F("Category");
        }
        //获取URL生成规则缓存
        $this->urlrules = F("urlrules");
        if(!$this->urlrules){
            D("Urlrule")->public_cache_urlrule();
            $this->urlrules = F("urlrules");
        }
    }

    /**
     * 首页链接
     * @param type $page 页码
     * @param type $type 类型，如果为URLRULE，则表示生成URL规则，其他则生成信息具体地址和生成文件存放路径。
     * @return array 0=>url , 1=>生成路径
     */
    public function index($page = 0, $type = "") {
        //取分页最大值
        $page = max($page, 1);
        //URL规则ID 
        $index_ruleid = CONFIG_INDEX_URLRULEID;
        $urlrules = $this->urlrules[$index_ruleid];
        //如果URL规则为空，为确保正常，默认一个动态URL规则
        if (!$urlrules) {
            $urlrules = 'index.php|index.php?page={$page}';
        }
        if ($type == "") {
            $urlrules_arr = explode("|", $urlrules);
            //判断是否为第一页
            if ($page == 1) {
                $urlrule = $urlrules_arr[0];
            } else {
                $urlrule = isset($urlrules_arr[1]) ? $urlrules_arr[1] : $urlrules_arr[0];
            }
            $urls = str_replace(array('{$page}'), array($page), $urlrule);
            $url_arr = array();
            //访问地址
            $url_arr[0] = CONFIG_SITEURL == '/' ? '/' . $urls : rtrim(CONFIG_SITEURL, '/') . '/' . $urls;
            //生成地址
            $url_arr[1] = '/' . $urls;

            $url = $url_arr;
        } else if ($type == "URLRULE") {
            $url = explode("|", $urlrules);
        }

        return $url;
    }

    /**
     * 内容页链接
     * @param $id 内容id
     * @param $page 当前页
     * @param $catid 栏目id
     * @param $time 真实的添加时间
     * @param $prefix 前缀
     * @param $data 数据
     * @param $action 操作方法
     * @param $type 类型，如果为URLRULE，则表示生成URL规则，其他则生成信息具体地址和生成文件存放路径。
     * @return array 0=>url , 1=>生成路径
     */
    public function show($id, $page = 0, $catid = 0, $time = 0, $prefix = '', $data = '', $action = 'edit', $type = "") {
        //取分页最大值
        $page = max($page, 1);
        $urls = $catdir = '';
        if (is_numeric($time) == false) {
            $time = strtotime($time);
        }

        //当前栏目信息
        $category = $this->categorys[$catid];
        //当前栏目setting配置信息
        $setting = unserialize($category['setting']);
        //是否生成内容静态
        $content_ishtml = $setting['content_ishtml'];
        //内容规则ID
        $show_ruleid = $setting['show_ruleid'];
        //取得URL规则
        $urlrules = $this->urlrules[$show_ruleid];
        if (empty($urlrules)) {
            return false;
        }
        if (!$time)
            $time = SYS_TIME;
        $urlrules_arr = explode('|', $urlrules);

        $domain_dir = '';
        //判断栏目地址是绑定域名，如果绑定了域名，内容页会生成到绑定域名的栏目下面
        if ($category['domain'] && strpos($category['domain'], '?') === false) {
            if (preg_match('/^((http|https):\/\/)?([^\/]+)/i', $category['url'], $matches)) {
                //取得栏目地址中域名部分
                $match_url = $matches[0];
                $domain_url = $match_url . '/';
            }
            $db = M("Category");
            //取得绑定这个域名的栏目ID
            $r['catid'] = $db->where(array('url' => $domain_url))->getField('catid');
            //取得顶级绑定域名的栏目目录
            if ($r) {
                //取得当前绑定域名栏目的父路径+当前栏目目录
                $domain_dir = $this->get_categorydir($r['catid']) . $this->categorys[$r['catid']]['catdir'] . '/';
            }
        }

        //取得当前栏目父目录路径
        $categorydir = $this->get_categorydir($catid);
        //栏目目录
        $catdir = $category['catdir'];
        //年份
        $year = date('Y', $time);
        //月份
        $month = date('m', $time);
        //日期
        $day = date('d', $time);

        //文件名，如果有自定义文件名则使用自定义文件名，否则默认使用当前内容ID
        if ($content_ishtml && $prefix) {
            $Filename = trim($prefix);
        } else {
            $Filename = $id;
        }

        //类型，为空表示生成信息具体地址和生成文件存放路径，下标1表示生成路径，0，表示信息地址
        if ($type == "") {
            //判断是否为第一页
            if ($page == 1) {
                $urlrule = $urlrules_arr[0];
            } else {
                $urlrule = isset($urlrules_arr[1]) ? $urlrules_arr[1] : $urlrules_arr[0];
            }
            //url地址，标签替换。
            $urls = str_replace(array('{$categorydir}', '{$catdir}', '{$year}', '{$month}', '{$day}', '{$catid}', '{$id}', '{$page}'), array($categorydir, $catdir, $year, $month, $day, $catid, $Filename, $page), $urlrule);
            //栏目绑定了域名，且需要生成静态
            if ($content_ishtml && $domain_url) {
                //绑定域名的栏目地址处理
                if ($domain_dir) {
                    //信息地址
                    $url_arr[0] = $domain_url . $urls;
                    //生成路径
                    $url_arr[1] = '/' . $domain_dir . $urls;
                }
            } elseif ($content_ishtml) {//正常栏目
                //信息地址
                $url_arr[0] = CONFIG_SITEURL == '/' ? '/' . $urls : rtrim(CONFIG_SITEURL, '/') . '/' . $urls;
                //生成路径
                $url_arr[1] = '/' . $urls;
            } else {
                $url_arr[0] = CONFIG_SITEURL . $urls;
                $url_arr[1] = "/";
            }
        } else if ($type == "URLRULE") { //生成信息的URL规则，用于分页导航
            $urls_index = str_replace(array('{$categorydir}', '{$catdir}', '{$year}', '{$month}', '{$day}', '{$catid}', '{$id}',), array($categorydir, $catdir, $year, $month, $day, $catid, $Filename,), $urlrules_arr[0]);
            $urls_list = str_replace(array('{$categorydir}', '{$catdir}', '{$year}', '{$month}', '{$day}', '{$catid}', '{$id}',), array($categorydir, $catdir, $year, $month, $day, $catid, $Filename,), $urlrules_arr[1]);
            //栏目绑定了域名，且需要生成静态
            if ($content_ishtml && $domain_url) {
                //绑定域名的栏目地址处理
                if ($domain_dir) {
                    //信息首页地址
                    $url_arr[0] = $domain_url . $urls_index;
                    //信息其他分页地址
                    $url_arr[1] = $domain_url . $urls_list;
                }
            } elseif ($content_ishtml) {//正常栏目
                //信息首页地址
                $url_arr[0] = CONFIG_SITEURL == '/' ? '/' . $urls_index : rtrim(CONFIG_SITEURL, '/') . '/' . $urls_index;
                //信息其他分页地址
                $url_arr[1] = CONFIG_SITEURL . $urls_list;
            } else {
                //信息首页地址
                $url_arr[0] = CONFIG_SITEURL . $urls_index;
                //信息其他分页地址
                $url_arr[1] = CONFIG_SITEURL . $urls_list;
            }
        }

        //生成静态 ,在添加文章的时候，同时生成静态，不在批量更新URL处调用
        if ($content_ishtml && $data) {
            $data['id'] = $id;
            $url_arr['content_ishtml'] = 1;
            $url_arr['data'] = $data;
        }
        return $url_arr;
    }

    /**
     * 获取栏目的访问路径
     * 在修复栏目路径处重建目录结构用
     * @param intval $catid 栏目ID
     * @param intval $page 页数
     * @param $type 类型，如果为URLRULE，则表示生成URL规则，其他则生成信息具体地址和生成文件存放路径。
     * @return array 0=>url , 1=>生成路径
     */
    public function category_url($catid, $page = 1, $type = '') {
        //栏目数据
        $category = $this->categorys[$catid];
        //外部链接直接返回外部地址
        if ($category['type'] == 2)
            return $category['url'];
        //页码
        $page = max(intval($page), 1);
        //栏目扩展配置信息反序列化
        $setting = unserialize($category['setting']);
        //栏目URL生成规则ID
        $category_ruleid = $setting['category_ruleid'];
        //取得规则
        $urlrules = $this->urlrules[$category_ruleid];
        //判断是URL规则缓存是否存在
        if (!$urlrules) {
            return false;
        }
        $urlrules_arr = explode('|', $urlrules);
        //判断是否首页
        if ($page == 1) {
            $urlrule = $urlrules_arr[0];
        } else {
            $urlrule = $urlrules_arr[1];
        }

        //标签替换
        //获取当前栏目父栏目路径
        $category_dir = $this->get_categorydir($catid);
        //取得栏目URL地址
        if ($type == "") {
            $urls = str_replace(array('{$categorydir}', '{$catdir}', '{$catid}', '{$page}'), array($category_dir, $category['catdir'], $catid, $page), $urlrule);
        } else if ($type == "URLRULE") {//生成UURLRULE，数组形式，下标0为首页，1为分页页
            $urls_index = str_replace(array('{$categorydir}', '{$catdir}', '{$catid}',), array($category_dir, $category['catdir'], $catid,), $urlrules_arr[0]);
            $urls_list = str_replace(array('{$categorydir}', '{$catdir}', '{$catid}',), array($category_dir, $category['catdir'], $catid,), $urlrules_arr[1]);
        }

        //检测是否要生成静态
        if (!$setting['ishtml']) { //如果不生成静态
            $url = array();
            if ($type == "") {
                $url[0] = CONFIG_SITEURL . $urls;
                if (strpos($urls, '\\') !== false) {
                    //不生成静态的情况下，直接网站url+地址
                    $url[0] = CONFIG_SITEURL . str_replace('\\', '/', $url);
                }
            } else if ($type == "URLRULE") {
                $url[0] = CONFIG_SITEURL . str_replace('\\', '/', $urls_index);
                $url[1] = CONFIG_SITEURL . str_replace('\\', '/', $urls_list);
            }
        } else { //生成静态
            //所有父ID
            if ($category['arrparentid']) {
                $parentids = explode(',', $category['arrparentid']);
            }

            //把自身栏目id加入到父id数组中
            $parentids[] = $catid;
            $domain_dir = '';

            foreach ($parentids as $pid) { //循环查询父栏目是否设置了二级域名
                $r = $this->categorys[$pid];
                if ($category['domain'] && strpos($category['domain'], '?') === false) {
                    $r['domain'] = preg_replace('/([(http|https):\/\/]{0,})([^\/]*)([\/]{1,})/i', '$1$2/', $r['domain'], -1); //取消掉双'/'情况
                    //二级域名
                    $domain = $r['domain'];
                    //得到二级域名的目录
                    $domain_dir = $this->get_categorydir($pid) . $this->categorys[$pid]['catdir'] . '/';
                }
            }

            if ($type == "") {
                $url = array();
                if ($domain && $domain_dir) {//绑定域名
                    if (strpos($urls, $domain_dir) === 0) {
                        $url[0] = str_replace(array($domain_dir, '\\'), array($domain, '/'), $urls);
                        $url[1] = "/" . $urls;
                    } else {
                        $urls = $domain_dir . $urls;
                        $url[0] = str_replace(array($domain_dir, '\\'), array($domain, '/'), $urls);
                        $url[1] = "/" . $urls;
                    }
                } else {
                    $url[0] = CONFIG_SITEURL . $urls;
                    $url[1] = "/" . $urls;
                }
                
            } else if ($type == "URLRULE") {
                $url = array();
                if ($domain && $domain_dir) {
                    if (strpos($urls_index, $domain_dir) === 0) {
                        $url[0] = str_replace(array($domain_dir, '\\'), array($domain, '/'), $urls_index);
                        $url[1] = str_replace(array($domain_dir, '\\'), array($domain, '/'), $urls_list);
                    } else {
                        $url[0] = $domain_dir . $urls_index;
                        $url[1] = $domain_dir . $urls_list;

                        $url[0] = str_replace(array($domain_dir, '\\'), array($domain, '/'), $url[0]);
                        $url[1] = str_replace(array($domain_dir, '\\'), array($domain, '/'), $url[1]);
                    }
                } else {
                    $url[0] = CONFIG_SITEURL . $urls_index;
                    $url[1] = CONFIG_SITEURL . $urls_list;
                }
                
            }
        }
        
         //判断是否为首页文件，如果是，就不显示文件名，隐藏
        if (in_array(basename($url[0]), array('index.html', 'index.htm', 'index.shtml'))) {
            $url[0] = dirname($url[0]) . '/';
        }
        if (strpos($url[0], '://') === false) {
            $url[0] = str_replace('//', '/', $url[0]);
        }
        if (strpos($url[0], '/') === 0) {
            $url[0] = substr($url[0], 1);
        }
        return $url;
    }

    /**
     * 根据栏目ID获取父栏目路径
     * @param $catid
     * @param $dir
     */
    public function get_categorydir($catid, $dir = '') {
        //检查这个栏目是否有父栏目ID
        if ($this->categorys[$catid]['parentid']) {
            //取得父栏目目录
            $dir = $this->categorys[$this->categorys[$catid]['parentid']]['catdir'] . '/' . $dir;
            return $this->get_categorydir($this->categorys[$catid]['parentid'], $dir);
        } else {
            return $dir;
        }
    }

    //根据栏目ID，取得栏目路径
    public function get_categorydirpath($catid) {
        if (!$catid) {
            return false;
        }
        $catdir = M("Category")->where(array("catid" => $catid))->getField("catdir");
        if (!$catdir) {
            return false;
        }
        $parent = $this->get_categorydir($catid);
        return $parent . $catdir . "/";
    }

}

?>