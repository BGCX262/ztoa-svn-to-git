<?php

/**
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class CategoryModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('modelid', 'require', '所属模型不能为空！', 1, 'regex', 3),
        array('catname', 'require', '栏目名称不能为空！', 1, 'regex', 3),
        array('catdir', 'require', '英文目录不能为空！', 1, 'regex', 3),
        array('catdir', 'checkCatdir', '目录名称已存在！', 1, 'callback', 1),
        array('setting', 'checkSetting', 'Setting配置信息有误！', 1, 'callback', 1),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
    );

    /**
     * 验证setting配置信息
     * @param type $setting
     * @return boolean
     */
    public function checkSetting($setting, $type = "") {
        $type = $type?$type:(int)$_REQUEST['type'];
        if($type == 2){
            return true;
        }
        if(!$setting){
            return true;
        }
        $setting = unserialize($setting);
        if ((!$setting['category_ruleid'] || !$setting['category_ruleid']) && (int) $type != 2) {
            return false;
        }
        return true;
    }

    /**
     * 检查目录是否存在 
     */
    public function checkCatdir($catdir, $catid = 0, $parentid = 0, $old_catdir = false ,$type = false) {
        $type = $type?$type:(int)$_REQUEST['type'];
        if($type == 2){
            return true;
        }
        $catid = $catid ? $catid : ($_REQUEST['info']['catid']?$_REQUEST['info']['catid']:$_REQUEST['catid']);
        //父ID
        $parentid = $parentid ? $parentid : intval($_REQUEST['info']['parentid']);
        //旧目录
        $old_catdir = $old_catdir ? $old_catdir : $_REQUEST['old_catdir'];
        //取得父目录
        import('Url');
        $Url = new Url();
        $parenpath = $Url->get_categorydirpath($parentid);
        $where = array("parentdir" => $parenpath, 'module' => 'content', 'catdir' => $catdir);
        $rs_catid = $this->where($where)->getField("catid");
        if ($rs_catid && $rs_catid != $catid) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 生成缓存，以栏目ID为数组下标，以排序字段listorder ASC排序
     */
    public function category_cache() {
        $models = F("Model");
        $data = $this->order("listorder ASC")->select();
        $categorys = array();
        foreach ($data as $r) {
            unset($r['module']);
            $setting = unserialize($r['setting']);
            //栏目生成Html
            $r['ishtml'] = $setting['ishtml'];
            //内容页生成Html
            $r['content_ishtml'] = $setting['content_ishtml'];
            //栏目页URL规则
            $r['category_ruleid'] = $setting['category_ruleid'];
            //内容也URL规则
            $r['show_ruleid'] = $setting['show_ruleid'];
            //工作流
//            $r['workflowid'] = $setting['workflowid'];
            $r['isdomain'] = '0';
            //判断栏目地址是否为(http|https)，也就是是否绑定了域名，或者外部链接栏目
            if (!preg_match('/^(http|https):\/\//', $r['url'])) {
                //本站域名后面不能带/
                $Domain = urlDomain(CONFIG_SITEURL);
                if ($Domain) {
                    $r['url'] = substr($Domain, 0, -1) . $r['url'];
                }
            }
            $categorys[$r['catid']] = $r;
        }

        F("Category", $categorys);
        unset($data, $models, $array);
        return true;
    }

    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Category", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->category_cache();
    }

    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->category_cache();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->category_cache();
    }

}

?>
