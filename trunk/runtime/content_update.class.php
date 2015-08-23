<?php

/* * 
 * 数据更新，也就是类似回调吧！
 * Some rights reserved：abc3210.com
 * Contact email:lanbinbin@85zu.com
 */

class content_update {

    public $modelid, $fields, $data, $id, $catid;
    //错误提示
    public $error;

    function __construct($modelid, $id) {
        $this->modelid = $modelid;
        $this->fields = F("Model_field_" . $modelid);
        $this->id = $id;
        load("@.treatfun");
    }

    /**
     * 执行更新操作
     * @param type $data
     */
    function update($data) {
        $info = array();
        $this->data = $data;
        $catid = $this->catid = (int) $data['catid'];
        foreach ($data as $field => $value) {
            if (!isset($this->fields[$field])) {
                continue;
            }
            $func = $this->fields[$field]['formtype'];
            //配置
            $setting = unserialize($this->fields[$field]['setting']);

            $value = method_exists($this, $func) ? $this->$func($field, $value) : $value;

            //字段扩展，可以对字段内容进行再次处理，类似ECMS字段处理函数
            if ($setting['backstagefun'] || $setting['frontfun']) {
                $backstagefun = explode("###", $setting['backstagefun']);
                $usfun = $backstagefun[0];
                $usparam = $backstagefun[1];
                //前后台
                if (defined("IN_ADMIN") && IN_ADMIN) {
                    //检查方法是否存在
                    if (function_exists($usfun)) {
                        //判断是入库执行类型
                        if ((int) $setting['backstagefun_type'] >= 2) {
                            //调用自定义函数，参数传入：模型id，栏目ID，信息ID，字段内容，字段名，操作类型，附加参数
                            // 例子 demo($modelid ,$value , $catid , $id, $field ,$action ,$param){}
                            try {
                                $value = call_user_func($usfun, $this->modelid, $this->id, $value, $field, ACTION_NAME, $usparam);
                            } catch (Exception $exc) {
                                //记录日志
                                Log::write("模型id:" . $this->modelid . ",错误信息：调用自定义函数" . $usfun . "出现错误！");
                            }
                        }
                    }
                } else {
                    //前台投稿处理自定义函数处理
                    //判断当前用户组是否拥有使用字段处理函数的权限，该功能暂时木有，以后加上
                    if (true) {
                        $backstagefun = explode("###", $setting['frontfun']);
                        $usfun = $backstagefun[0];
                        $usparam = $backstagefun[1];
                        //检查方法是否存在
                        if (function_exists($usfun)) {
                            //判断是入库执行类型
                            if ((int) $setting['backstagefun_type'] >= 2) {
                                //调用自定义函数，参数传入：模型id，栏目ID，信息ID，字段内容，字段名，操作类型，附加参数
                                // 例子 demo($modelid ,$value , $catid , $id, $field ,$action ,$param){}
                                try {
                                    $value = call_user_func($usfun, $this->modelid, $this->id, $value, $field, ACTION_NAME, $usparam);
                                } catch (Exception $exc) {
                                    //记录日志
                                    Log::write("模型id:" . $this->modelid . ",错误信息：调用自定义函数" . $usfun . "出现错误！");
                                }
                            }
                        }
                    }
                }
            }

            $info[$field] = $value;
        }
        
        return $info;
    }

    /**
     * 错误信息
     * @param type $message 错误信息
     * @param type $fields 字段
     */
    public function error($message, $fields = false) {
        $this->error = $message;
    }

    /**
     * 获取错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }



/**
 * 关键字整理，增加到TAG表
 * Some rights reserved：abc3210.com
 * Contact email:lanbinbin@85zu.com
 */
function keyword($field, $value) {
    if (!empty($value)) {
        $db = M("Tags");
        $time = time();
        if (strpos($value, ',') === false) {
            $keyword = explode(' ', $value);
        } else {
            $keyword = explode(',', $value);
        }
        $keyword = array_unique($keyword);
        $data = array();
        //新增
        if (ACTION_NAME == 'add') {
            foreach ($keyword as $v) {
                if (empty($v) || $v == '') {
                    continue;
                }
                if ($db->where(array("tag" => $v))->find()) {
                    $db->where(array("tag" => $v))->setInc('usetimes');
                } else {
                    $db->data(array(
                        "tag" => $v,
                        "usetimes" => 1,
                        "lastusetime" => $time,
                        "lasthittime" => $time,
                    ))->add();
                }
                $data[] = array(
                    'tag' => $v,
                    "url" => $this->data['url'],
                    "title" => $this->data['title'],
                    "modelid" => $this->modelid,
                    "contentid" => $this->id,
                    "catid" => $this->data['catid'],
                    "updatetime" => $time,
                );
            }
            M("Tags_content")->addAll($data);
        } else {
            $tags = M("Tags_content")->where(array(
                        "modelid" => $this->modelid,
                        "contentid" => $this->id,
                        "catid" => $this->data['catid'],
                    ))->select();
            foreach ($tags as $key => $value) {
                //如果在新的关键字数组找不到，说明已经去除
                if (!in_array($value['tag'], $keyword)) {
                    //删除不存在的tag
                    M("Tags_content")->where(array("tag" => $value['tag'], "modelid" => $value['modelid'], "contentid" => $value['contentid'], "catid" => $value['catid']))->delete();
                    $db->where(array("tag" => $value['tag']))->setDec('usetimes');
                } else {
                    //更新URL
                    M("Tags_content")->where(array("tag" => $value['tag'], "modelid" => $value['modelid'], "contentid" => $value['contentid'], "catid" => $value['catid']))->data(array("url" => $this->data['url']))->save();
                    foreach ($keyword as $k => $v) {
                        if ($value['tag'] == $v) {
                            unset($keyword[$k]);
                        }
                    }
                }
            }
            //新增的tags
            if (count($keyword) > 0) {
                foreach ($keyword as $v) {
                    if (empty($v) || $v == '') {
                        continue;
                    }
                    if ($db->where(array("tag" => $v))->find()) {
                        $db->where(array("tag" => $v))->setInc('usetimes');
                    } else {
                        $db->data(array(
                            "tag" => $v,
                            "usetimes" => 1,
                            "lastusetime" => $time,
                            "lasthittime" => $time,
                        ))->add();
                    }
                    $data[] = array(
                        'tag' => $v,
                        "url" => $this->data['url'],
                        "title" => $this->data['title'],
                        "modelid" => $this->modelid,
                        "contentid" => $this->id,
                        "catid" => $this->data['catid'],
                        "updatetime" => $time,
                    );
                }
                M("Tags_content")->addAll($data);
            }
        }
    }
}



//推荐位数据处理
function posid($field, $value) {
    if (!empty($value) && is_array($value)) {
        //新增
        if (ACTION_NAME == 'add') {
            $position_data_db = M('Position_data');
            $textcontent = array();
            foreach ($value as $r) {
                if ($r != '-1') {
                    if (empty($textcontent)) {
                        foreach ($this->fields AS $_key => $_value) {
                            //判断字段是否入库到推荐位字段
                            if ($_value['isposition']) {
                                $textcontent[$_key] = $this->data[$_key];
                            }
                        }
                    }
                    //颜色选择为隐藏域 在这里进行取值
                    $textcontent['style'] = $_POST['style_color'] ? strip_tags($_POST['style_color']) : '';
                    $textcontent = serialize($textcontent);
                    $data = array('id' => $this->id, 'catid' => $this->data['catid'], 'posid' => $r, 'module' => 'content', 'modelid' => $this->modelid, 'data' => $textcontent, 'listorder' => $this->id);
                    //增加
                    $status = $position_data_db->data($data)->add();
                }
            }
        } else {
            $posids = array();
            $catid = $this->data['catid'];
            $position_data_db = D('Position');
            foreach ($value as $r) {
                if ($r != '-1')
                    $posids[] = $r;
            }
            $textcontent = array();
            foreach ($this->fields AS $_key => $_value) {
                if ($_value['isposition']) {
                    $textcontent[$_key] = $this->data[$_key];
                }
            }
            //颜色选择为隐藏域 在这里进行取值
            $textcontent['style'] = $_POST['style_color'] ? strip_tags($_POST['style_color']) : '';
            
            $position_data_db->position_update($this->id, $this->modelid, $catid, $posids, $textcontent);
        }
    }
}


 } 
?>