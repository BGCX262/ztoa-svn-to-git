<?php

/**
 * 前台Action
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class BaseAction extends AppframeAction {

    public $TemplatePath, $Theme, $ThemeDefault;

    function _initialize() {
        parent::_initialize();
        //定义是前台
        define('IN_ADMIN', false);
        //前台关闭表单令牌
        C("TOKEN_ON",false);
        $this->initUser();
        //初始化模型
        $this->initModel();
        $this->tmpinit();
        //============全局模板变量==============
        //栏目数组
        $this->assign("Categorys",F("Category"));
        //模型数组
        $this->assign("Model",F("Model"));
        //推荐位数组
        $this->assign("Position",F("Position"));
        //URL规则数组
        $this->assign("Urlrules",F("urlrules"));
    }

    /**
     * 模板配置初始化 
     */
    final private function tmpinit() {
        //模板路径
        $this->TemplatePath = TEMPLATE_PATH;
        //默认主题风格
        $this->ThemeDefault = "Default";
        //主题风格
        $this->Theme = empty(AppframeAction::$Cache["Config"]['theme']) ? $this->ThemeDefault : AppframeAction::$Cache["Config"]['theme'];
        //设置前台提示信息模板
        if (file_exists_case($this->TemplatePath . $this->Theme . "/" . "error".C("TMPL_TEMPLATE_SUFFIX")) && IN_ADMIN == false) {
            C("TMPL_ACTION_ERROR", $this->TemplatePath . $this->Theme . "/" . "error".C("TMPL_TEMPLATE_SUFFIX"));
        }
        if (file_exists_case($this->TemplatePath . $this->Theme . "/" . "success".C("TMPL_TEMPLATE_SUFFIX")) && IN_ADMIN == false) {
            C("TMPL_ACTION_SUCCESS", $this->TemplatePath . $this->Theme . "/" . "success".C("TMPL_TEMPLATE_SUFFIX"));
        }
    }

     /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $templateFile 模板文件名
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $content 模板输出内容
     * @return mixed
     */
    public function display($templateFile='',$charset='',$contentType='',$content='') { 
        parent::display($this->parseTemplateFile($templateFile), $charset, $contentType);
    }

    /**
     * 模板路径
     * @param type $templateFile
     * @return boolean|string 
     */
    public function parseTemplateFile($templateFile = '') {
        $status = parseTemplateFile($templateFile);
        if($status == false) {
            echo str_replace(SITE_PATH, "", $templateFile) . "模板不存在！";
            exit;
        }
        return $status;
    }

    /**
     * 分页
     * @param type $Total_Size
     * @param type $Page_Size
     * @param type $Current_Page
     * @param type $listRows
     * @param type $PageParam
     * @param type $PageLink
     * @param type $Static
     * @return \Page 
     */
    protected function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 1, $listRows = 6, $PageParam = '', $PageLink = '', $Static = FALSE) {
        import('Page');
        if ($Page_Size == 0) {
            $Page_Size = C("PAGE_LISTROWS");
        }
        if (empty($PageParam)) {
            $PageParam = C("VAR_PAGE");
        }
        $Page = new Page($Total_Size, $Page_Size, $Current_Page, $listRows, $PageParam, $PageLink, $Static);
        $Page->SetPager('Admin', '共有{recordcount}条信息&nbsp;{pageindex}/{pagecount}&nbsp;{first}{prev}&nbsp;{liststart}{list}{listend}&nbsp;{next}{last}&nbsp;转到{jump}页', array("listlong" => "6", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
        return $Page;
    }

}

?>
