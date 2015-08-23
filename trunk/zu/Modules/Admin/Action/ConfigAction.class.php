<?php

/**
 * 网站配置信息管理
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class ConfigAction extends AdminbaseAction {

    protected $site_config, $user_config, $Config;

    function _initialize() {
        parent::_initialize();
        $this->Config = D("Config");
        import('Form');
        $config = $this->Config->select();
        foreach ($config as $key => $r) {
            if ($r['groupid'] == 1)
                $this->user_config[$r['varname']] = Input::forShow($r['value']);
            if ($r['groupid'] == 2)
                $this->site_config[$r['varname']] = Input::forShow($r['value']);
        }
        $this->assign('Site', $this->site_config);
    }

    /**
     * 网站基本设置
     */
    public function index() {
        if (IS_POST) {
            $this->dosite();
        } else {
            //首页模板
            $filepath = TEMPLATE_PATH . (empty(AppframeAction::$Cache["Config"]['theme']) ? "Default" : AppframeAction::$Cache["Config"]['theme']) . "/Contents/Index/";
            $indextp = str_replace($filepath, "", glob($filepath . 'index*'));
            $urlrules_detail = F("urlrules_detail");
            $IndexURL = array();
            $TagURL = array();
            foreach ($urlrules_detail as $k => $v) {
                if ($v['module'] == 'tags' && $v['file'] == 'tags') {
                    $TagURL[$v['urlruleid']] = $v['example'];
                }
                if ($v['module'] == 'content' && $v['file'] == 'index') {
                    $IndexURL[$v['ishtml']][$v['urlruleid']] = $v['example'];
                }
            }

            $this->assign("TagURL", $TagURL);
            $this->assign("IndexURL", $IndexURL);
            $this->assign("indextp", $indextp);
            $this->display();
        }
    }

    /**
     *  系统参数
     */
    public function sys() {
        if (IS_POST) {
            $this->dosite();
        } else {
            $this->display();
        }
    }

    /**
     *  邮箱参数
     */
    public function mail() {
        if (IS_POST) {
            $this->dosite();
        } else {
            $this->display();
        }
    }

    /**
     *  附件参数
     */
    public function attach() {
        if (IS_POST) {
            $this->dosite();
        } else {
            $config = $this->Config->select();
            foreach ($config as $key => $r) {
                if ($r['groupid'] == 1)
                    $this->user_config[$r['varname']] = Input::forShow($r['value']);
                if ($r['groupid'] == 2)
                    $this->site_config[$r['varname']] = Input::forShow($r['value']);
            }
            $this->assign('Site', $this->site_config);
            $this->display();
        }
    }

    //更新配置
    protected function dosite() {
        if (!$this->Config->autoCheckToken($_POST)) {
            $this->error("令牌验证失败！");
        }
        unset($_POST[C("TOKEN_NAME")]);
        foreach ($_POST as $key => $value) {
            $data["value"] = trim($value);
            $this->Config->where(array("varname" => $key))->save($data);
        }
        $this->success("更新成功！");
    }

}

?>
