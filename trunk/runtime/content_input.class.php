<?php

/* * 
 * 处理数据，为入库前做准备
 * Some rights reserved：abc3210.com
 * Contact email:lanbinbin@85zu.com
 */

class content_input {

    public $modelid, $fields, $data, $catid;
    //错误提示
    public $error = "";

    /**
     * 构造函数
     * @param type $modelid 模型ID
     * @param type $Action 传入this
     */
    function __construct($modelid) {
        $this->modelid = $modelid;
        $this->fields = F("Model_field_" . $modelid);
        load("@.treatfun");
    }

    /**
     * 数据入库前处理
     * @param type $data
     * @return boolean|string 
     */
    function get($data) {
        $this->data = $data;
        $catid = $this->catid = (int) $data['catid'];
        //检测模型ID是否存在
        if (!$this->modelid) {
            $this->error("模型ID不存在！");
        }
        $info = array();
        foreach ($data as $field => $value) {
            //字段别名
            $name = $this->fields[$field]['name'];
            //最小值
            $minlength = $this->fields[$field]['minlength'];
            //最大值
            $maxlength = $this->fields[$field]['maxlength'];
            //数据校验正则
            $pattern = $this->fields[$field]['pattern'];
            //数据校验未通过的提示信息
            $errortips = $this->fields[$field]['errortips'];
            //配置
            $setting = unserialize($this->fields[$field]['setting']);

            //如果没有错误提示，默认一个
            if (empty($errortips)){
                $errortips = $name . ' 不符合要求！';
            }
            //数据长度
            $length = empty($value) ? 0 : strlen($value);
            //进行长度验证
            if ($minlength && $length < $minlength) {
                $this->error($name . ' 不得少于 ' . $minlength . "个字符！");
                return false;
            }
            if ($maxlength && $length > $maxlength) {
                $this->error($name . ' 不得多余 ' . $maxlength . "个字符！");
                return false;
            } elseif ($maxlength) {
                //字符截取
                $value = str_cut($value, $maxlength, '');
            }

            if ($pattern && $length && !preg_match($pattern, $value)) {
                $this->error($errortips, $field);
                return false;
            }

            //实例化模型
            try {
                //取得模型缓存
                $MODEL = F("Model");
                if (!$MODEL) {
                    D("Model")->model_cache();
                    $MODEL = F("Model");
                }
                //判断是否内容模块调用，如果是，则判断主表，副表
                if ($MODEL[$this->modelid]) {
                    //获取表名 先判断是主表还是副表
                    $this->table_name = $this->fields[$field]['issystem'] ? $MODEL[$this->modelid]['tablename'] : $MODEL[$this->modelid]['tablename'] . '_data';
                    //实例化数据库链接
                    $this->Content = M(ucwords($this->table_name));
                } else {
                    $this->error("该模型不存在！");
                    return false;
                }
            } catch (Exception $exc) {
                $this->error("模型实例化错误！");
                return false;
            }
            
            //值唯一检测
            if ($this->fields[$field]['isunique'] && ($this->Content->where(array($field => $value))->count()) > 0 && ACTION_NAME != 'edit') {
                $this->error($name . "该值必须不重复！");
                return false;
            }
            //字段类型
            $func = $this->fields[$field]['formtype'];

            //检测对应字段方法是否存在，存在则执行此方法，并传入字段名和字段值
            if (method_exists($this, $func)) {
                $value = $this->$func($field, $value);
                //当没有返回时，或者为 null 时，等于空字符串，null有时会出现mysql 语法错误。
                if (is_null($value)) {
                    $value = "";
                }
            }

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
                        if ((int) $setting['backstagefun_type'] == 1 || (int) $setting['backstagefun_type'] == 3) {
                            //调用自定义函数，参数传入：模型id，栏目ID，信息ID，字段内容，字段名，操作类型，附加参数
                            // 例子 demo($modelid ,$value , $catid , $id, $field ,$action ,$param){}
                            $id = 0;
                            try {
                                $value = call_user_func($usfun, $this->modelid, $catid, $id, $value, $field, ACTION_NAME, $usparam);
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
                            if ((int) $setting['backstagefun_type'] == 1 || (int) $setting['backstagefun_type'] == 3) {
                                //调用自定义函数，参数传入：模型id，栏目ID，信息ID，字段内容，字段名，操作类型，附加参数
                                // 例子 demo($modelid ,$value , $catid , $id, $field ,$action ,$param){}
                                $id = 0;
                                try {
                                    $value = call_user_func($usfun, $this->modelid, $catid, $id, $value, $field, ACTION_NAME, $usparam);
                                } catch (Exception $exc) {
                                    //记录日志
                                    Log::write("模型id:" . $this->modelid . ",错误信息：调用自定义函数" . $usfun . "出现错误！");
                                }
                            }
                        }
                    }
                }
            }

            //把系统字段和模型字段分开
            if ($this->fields[$field]['issystem']) {
                $info['system'][$field] = $value;
            } else {
                $info['model'][$field] = $value;
            }

            //颜色选择为隐藏域 在这里进行取值
            $info['system']['style'] = $_POST['style_color'] ? strip_tags($_POST['style_color']) : '';
            //标题加粗等样式
            if ($_POST['style_font_weight']){
                $info['system']['style'] = $info['system']['style'] . ';' . strip_tags($_POST['style_font_weight']);
            }
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



function textarea($field, $value) {
    if (!$this->fields[$field]['enablehtml'])
        $value = strip_tags($value);
    return $value;
}



function editor($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    $isadmin = 0;
    //是否保存远程图片
    $enablesaveimage = (int)$setting['enablesaveimage'];
    if (defined("IN_ADMIN") && IN_ADMIN) {
        $isadmin = 1;
    }
    if($enablesaveimage){
        $Attachment = service("Attachment", array(
            "module" => "Contents",
            "catid" => $this->catid,
            "isadmin" => $isadmin,
        ));
        $value = $Attachment->download($value);
    }
    return $value;
}



function box($field, $value) {
    if ($this->fields[$field]['boxtype'] == 'checkbox') {
        if (!is_array($value) || empty($value))
            return false;
        array_shift($value);
        $value = ',' . implode(',', $value) . ',';
        return $value;
    } elseif ($this->fields[$field]['boxtype'] == 'multiple') {
        if (is_array($value) && count($value) > 0) {
            $value = ',' . implode(',', $value) . ',';
            return $value;
        }
    } else {
        return $value;
    }
}



function image($field, $value) {
    return trim($value);
}



function images($field, $value) {
    //取得图片列表
    $pictures = $_POST[$field . '_url'];
    //取得图片说明
    $pictures_alt = isset($_POST[$field . '_alt']) ? $_POST[$field . '_alt'] : array();
    $array = $temp = array();
    if (!empty($pictures)) {
        foreach ($pictures as $key => $pic) {
            $temp['url'] = $pic;
            $temp['alt'] = $pictures_alt[$key];
            $array[$key] = $temp;
        }
    }
    $array = serialize($array);
    return $array;
}



function datetime($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    if ($setting['fieldtype'] == 'int') {
        $value = strtotime($value);
    }
    return $value;
}



function copyfrom($field, $value) {
    $field_data = $field . '_data';
    if (isset($_POST[$field_data])) {
        $value .= '|' . $_POST[$field_data];
    }
    return $value;
}



function posid($field, $value) {
    $number = count($value);
    $value = $number == 1 ? 0 : 1;
    return $value;
}




function linkfield($field, $value) {
        return $value;
}



function downfiles($field, $value) {
    $files = $_POST[$field . '_fileurl'];
    $files_alt = $_POST[$field . '_filename'];
    if (defined("IN_ADMIN") && IN_ADMIN) {
        $groupid = $_POST[$field . '_groupid'];
        $point = $_POST[$field . '_point'];
    } else {
        $groupid = array();
        $point = array();
    }
    $array = $temp = array();
    if (!empty($files)) {
        foreach ($files as $key => $file) {
            $temp['fileurl'] = $file;
            $temp['filename'] = $files_alt[$key];
            $temp['groupid'] = $groupid[$key]?$groupid[$key]:0;
            $temp['point'] = $point[$key]?$point[$key]:0;
            $array[$key] = $temp;
        }
    }
    $array = serialize($array);
    return $array;
}



function map($field, $value) {
    foreach ($value as $r) {
        $data.='|' . $r;
    }
    return substr($data, 1);
}


 } 
?>