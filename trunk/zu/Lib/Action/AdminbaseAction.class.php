<?php

/**
 * 后台Action
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
//定义是后台
define('IN_ADMIN', true);

class AdminbaseAction extends AppframeAction {

    function _initialize() {
        //载入RBAC权限控制类
        import('RBAC');
        import('Page_1');
        parent::_initialize();
        //初始化当前登录用户信息
        $this->initUser();
        //初始化模型
        $this->initModel();
        //角色表名称
        C("RBAC_ROLE_TABLE", C("DB_PREFIX") . "role");
        //用户表名称
        C("RBAC_USER_TABLE", C("DB_PREFIX") . "role_user");
        //节点表名称
        C("RBAC_NODE_TABLE", C("DB_PREFIX") . "node");
        //后台用户模型
        C("USER_AUTH_MODEL", "User");
        //认证网关
        C("USER_AUTH_GATEWAY", U("Admin/Public/login"));
        $this->initMenu();

    }

    /**
     * 消息提示
     * @param type $message
     * @param type $jumpUrl
     * @param type $ajax 
     */
    public function success($message, $jumpUrl = '', $ajax = false) {
        parent::success($message, $jumpUrl, $ajax);
        $text = "应用：" . GROUP_NAME . ",模块：" . MODULE_NAME . ",方法：" . ACTION_NAME . "<br>提示语：" . $message;
        $this->addLogs($text);
    }

    /**
     * 模板显示
     * @param type $templateFile 指定要调用的模板文件
     * @param type $charset 输出编码
     * @param type $contentType 输出类型
     * @param string $content 输出内容
     * 此方法作用在于实现后台模板直接存放在各自项目目录下。例如Admin项目的后台模板，直接存放在Admin/Tpl/目录下
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '') {
        parent::display($templateFile, $charset, $contentType, $content);
    }

    //扩展方法，当用户没有权限操作，用于记录日志的扩展方法
    public function _ErrorLog() {
        
    }

    /**
     * 初始化后台菜单
     */
    private function initMenu() {
        $Menu = F("Menu");
        if (!$Menu) {
            D("Menu")->menu_cache();
        }
    }

    /**
     *  排序 排序字段为listorders数组 POST 排序字段为：listorder
     */
    protected function listorders($model) {
        if (!is_object($model)) {
            return false;
        }
        $pk = $model->getPk(); //获取主键名称
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $model->where(array($pk => $key))->save($data);
        }
        return true;
    }

    protected function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 1, $listRows = 6, $PageParam = '', $PageLink = '', $Static = FALSE) {
        import('Page');
        if ($Page_Size == 0) {
            $Page_Size = C("PAGE_LISTROWS");
        }
        if (empty($PageParam)) {
            $PageParam = C("VAR_PAGE");
        }
        $Page = new Page_1($Total_Size, $Page_Size, $Current_Page, $listRows, $PageParam, $PageLink, $Static);
        $Page->SetPager('Admin', '{first}{prev}&nbsp;{liststart}{list}{listend}&nbsp;{next}{last}', array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
        return $Page;
    }

    protected function page1($Total_Size = 1, $Page_Size = 0, $Current_Page = 1, $listRows = 6, $PageParam = '', $PageLink = '', $Static = FALSE) {
        import('Page');
        if ($Page_Size == 0) {
            $Page_Size = C("PAGE_LISTROWS");
        }
        if (empty($PageParam)) {
            $PageParam = C("VAR_PAGE");
        }
        $Page = new Page_1($Total_Size, $Page_Size, $Current_Page, $listRows, $PageParam, $PageLink, $Static);
        $Page->SetPager('Admin','共有{recordcount} 条记录&nbsp;&nbsp;每页显示{pagesize}条记录&nbsp;&nbsp;当前第&nbsp;{pageindex}&nbsp;页&nbsp;/&nbsp;共&nbsp;{pagecount}&nbsp;页&nbsp;&nbsp;{first}{prev}&nbsp;&nbsp;{list}&nbsp;&nbsp;{next}{last}&nbsp;&nbsp;转到&nbsp;{jump}&nbsp;页',array("listlong"=>"4","first"=>"首页","last"=>"尾页","prev"=>"上一页","next"=>"下一页","list"=>"第*页","jump"=>"select","jumplong"=>"0"));
        return $Page;
    }

    protected function page2($Total_Size,$Page_Size,$_p){
        $p = new Page($Total_Size,$Page_Size,$_p);
        return $p;
    }



    /**
     * 获取菜单导航
     * @param type $app
     * @param type $model
     * @param type $action
     */
    public static function getMenu() {

        $menuid = (int) $_GET['menuid'];
        $menuid = $menuid ? $menuid : cookie("menuid", "", array("prefix" => ""));
        //cookie("menuid",$menuid);

        $db = D("Menu");
        $info = $db->cache(true, 60)->where(array("id" => $menuid))->getField("id,action,app,model,parentid,data,type,name");
        $find = $db->cache(true, 60)->where(array("parentid" => $menuid, "status" => 1))->getField("id,action,app,model,parentid,data,type,name");

        if ($find) {
            array_unshift($find, $info[$menuid]);
        } else {
            $find = $info;
        }
        foreach ($find as $k => $v) {
            $find[$k]['data'] = "menuid=$menuid&" . $find[$k]['data'].genRandomString(4,2);
        }

        return $find;
    }

    /**
     * 当前位置
     * @param $id 菜单id
     */
    final public static function current_pos($id) {
        $menudb = M("Menu");
        $r = $menudb->where(array('id' => $id))->find();
        $str = '';
        if ($r['parentid']) {
            $str = self::current_pos($r['parentid']);
        }
        return $str . $r['name'] . ' > ';
    }

}

?>
