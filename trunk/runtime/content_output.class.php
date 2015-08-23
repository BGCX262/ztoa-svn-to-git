<?php

/**
 * 数据读取，主要用于前台数据显示
 * Some rights reserved：abc3210.com
 * Contact email:lanbinbin@85zu.com
 */
class content_output {
    
    public $modelid, $fields, $data, $catid, $categorys, $id;

    function __construct($modelid, $catid = 0, $categorys = array()) {
        $this->modelid = $modelid;
        $this->catid = $catid;
        $this->categorys = $categorys;
        $this->fields = F("Model_field_" . $modelid);
    }

    function get($data) {
        $this->data = $data;
        $this->id = $data['id'];
        $info = array();
        foreach ($this->fields as $field => $v) {
            if (!isset($data[$field])){
                continue;
            }
            $func = $v['formtype'];
            $value = $data[$field];
            $result = method_exists($this, $func) ? $this->$func($field, $data[$field]) : $data[$field];
            if ($result !== false)
                $info[$field] = $result;
        }
        return $info;
    }

//结尾 需要变成 }? > 


function editor($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    
    return $value;
}



function title($field, $value) {
    $value = htmlspecialchars($value);
    return $value;
}



function box($field, $value) {
    extract(unserialize($this->fields[$field]['setting']));
    if ($outputtype) {
        return $value;
    } else {
        $options = explode("\n", $this->fields[$field]['options']);
        foreach ($options as $_k) {
            $v = explode("|", $_k);
            $k = trim($v[1]);
            $option[$k] = $v[0];
        }
        $string = '';
        switch ($this->fields[$field]['boxtype']) {
            case 'radio':
                $string = $option[$value];
                break;

            case 'checkbox':
                $value_arr = explode(',', $value);
                foreach ($value_arr as $_v) {
                    if ($_v)
                        $string .= $option[$_v] . ' 、';
                }
                break;

            case 'select':
                $string = $option[$value];
                break;

            case 'multiple':
                $value_arr = explode(',', $value);
                foreach ($value_arr as $_v) {
                    if ($_v)
                        $string .= $option[$_v] . ' 、';
                }
                break;
        }
        return $string;
    }
}



function images($field, $value) {
    return unserialize($value);
}



function datetime($field, $value) {
    $setting = unserialize($this->fields[$field]['setting']);
    extract($setting);
    if ($fieldtype == 'date' || $fieldtype == 'datetime') {
        return $value;
    } else {
        $format_txt = $format;
    }
    if (strlen($format_txt) < 6) {
        $isdatetime = 0;
    } else {
        $isdatetime = 1;
    }
    if (!$value)
        $value = time();
    $value = date($format_txt, $value);
    return $value;
}



function keyword($field, $value) {
    if ($value == '')
        return '';
    $v = '';
    if (strpos($value, ',') === false) {
        $tags = explode(' ', $value);
    } else {
        $tags = explode(',', $value);
    }
    return $tags;
}



function copyfrom($field, $value) {
    static $copyfrom_array;
    
    $copyfrom_array = array();
    
    if ($value && strpos($value, '|') !== false) {
        $arr = explode('|', $value);
        $value = $arr[0];
        $value_data = $arr[1];
    }
    if ($value_data) {
        $copyfrom_link = $copyfrom_array[$value_data];
        if (!empty($copyfrom_array)) {
            $imgstr = '';
            if ($value == '')
                $value = $copyfrom_link['siteurl'];
            if ($copyfrom_link['thumb'])
                $imgstr = "<a href='{$copyfrom_link['siteurl']}' target='_blank'><img src='{$copyfrom_link['thumb']}' height='15'></a> ";
            return $imgstr . "<a href='$value' target='_blank' style='color:#AAA'>{$copyfrom_link['sitename']}</a>";
        }
    } else {
        return $value;
    }
}



function linkfield($field, $value) {
    return $value;
}



function downfiles($field, $value) {
    extract(unserialize($this->fields[$field]['setting']));
    $list_str = array();
    $file_list = unserialize($value);
    if (is_array($file_list)) {
        foreach ($file_list as $_k => $_v) {
            if ($_v['fileurl']) {
                if($downloadlink){
                    //链接到跳转页面
                    $fileurl = CONFIG_SITEURL."index.php?m=Download&a=index&catid=".$this->catid."&id=".$this->id."&f=$field&k=$_k";
                }else{
                    $fileurl = $_v['fileurl'];
                }
                $filename = $_v['filename'] ? $_v['filename'] : "点击下载";
                $groupid = $_v['groupid'] ? $_v['groupid'] : 0;
                $point = $_v['point'] ? $_v['point'] : 0;
                $list_str[$_k]['fileurl'] = $fileurl;
                $list_str[$_k]['filename'] = $filename;
                $list_str[$_k]['groupid'] = $groupid;
                $list_str[$_k]['point'] = $point;
            }
        }
    }
    return $list_str;
}



function map($field, $value) {
    $data = explode('|', $value);
    return $data;
}


 } 
?>