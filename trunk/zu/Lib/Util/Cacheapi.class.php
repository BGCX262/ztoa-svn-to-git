<?php

/* * 
 * 缓存更新
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */

class Cacheapi {

    function __call($name, $arguments) {
        Log::write("调用Cacheapi类中未定义方法:".$name."！");
        return false;
    }

    //重新生成  Category Model Menu
    public function site_cache() {
        D("Config")->config_cache();
        D("Category")->category_cache();
        D("Model")->model_cache();
        //后台菜单
        D("Menu")->menu_cache();
        //评论配置缓存
        D("Comments")->comments_cache();
        //URL规则
        $this->urlrule_cache();
    }

    //会员相关缓存
    public function member_cache() {
        //生成会员模型
        D("Model")->MemberModelCache();
        //会员组
        D("Member_group")->Membergroup_cache();
        //会员模型
        D("Model")->MemberModelCache();
        //会员模型配置信息
        $setting = M("Module")->where(array("module" => "Member"))->getField("setting");
        F("Member_Config", unserialize($setting));
    }

    // 更新模型缓存方法
    public function model_content_cache() {
        D("Content_cache")->model_content_cache();
        //推荐位缓存更新
        $this->position_cache();
    }

    //生成模型字段缓存 
    public function model_field_cache() {
        D("Model_field")->model_field_cache();
    }

    //生成 Urlrule URL规则缓存 
    public function urlrule_cache() {
        D("Urlrule")->public_cache_urlrule();
    }

    //推荐位缓存
    public function position_cache() {
        D("Position")->position_cache();
    }

    //可用应用列表缓存 
    public function appstart_cache() {
        D("Module")->module_cache();
    }

    //生成敏感词缓存
    public function censorword_cache() {

        $banned = $mod = array();
        $bannednum = $modnum = 0;
        $data = array('filter' => array(), 'banned' => '', 'mod' => '');

        foreach (M("CensorWord")->select() as $censor) {
            if (preg_match('/^\/(.+?)\/$/', $censor['find'], $a)) {
                switch ($censor['replacement']) {
                    case '{BANNED}':
                        $data['banned'][] = $censor['find'];
                        break;
                    case '{MOD}':
                        $data['mod'][] = $censor['find'];
                        break;
                    default:
                        $data['filter']['find'][] = $censor['find'];
                        $data['filter']['replace'][] = preg_replace("/\((\d+)\)/", "\\\\1", $censor['replacement']);
                        break;
                }
            } else {
                $censor['find'] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($censor['find'], '/'));
                switch ($censor['replacement']) {
                    case '{BANNED}':
                        $banned[] = $censor['find'];
                        $bannednum++;
                        if ($bannednum == 1000) {
                            $data['banned'][] = '/(' . implode('|', $banned) . ')/i';
                            $banned = array();
                            $bannednum = 0;
                        }
                        break;
                    case '{MOD}':
                        $mod[] = $censor['find'];
                        $modnum++;
                        if ($modnum == 1000) {
                            $data['mod'][] = '/(' . implode('|', $mod) . ')/i';
                            $mod = array();
                            $modnum = 0;
                        }
                        break;
                    default:
                        $data['filter']['find'][] = '/' . $censor['find'] . '/i';
                        $data['filter']['replace'][] = $censor['replacement'];
                        break;
                }
            }
        }

        if ($banned) {
            $data['banned'][] = '/(' . implode('|', $banned) . ')/i';
        }
        if ($mod) {
            $data['mod'][] = '/(' . implode('|', $mod) . ')/i';
        }

        F("Censor_words", $data);
        $typedata = M("Terms")->where(array("module" => "censor"))->select();
        F("Censor_type", $typedata);
        return $data;
    }

}

?>
