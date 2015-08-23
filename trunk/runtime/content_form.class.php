<?php

/**
 * 字段显示输入表单类
 * Some rights reserved：abc3210.com
 * Contact email:lanbinbin@85zu.com
 */
class content_form {

    public $modelid, $fields, $id, $categorys, $catid, $formValidateRules, $formValidateMessages, $formJavascript;

    /**
     * 构造函数
     * @param type $modelid 模型ID
     * @param type $catid 栏目ID
     * @param type $categorys 栏目数据
     */
    function __construct($modelid, $catid = 0, $categorys = array()) {
        $this->modelid = $modelid;
        $this->catid = $catid;
        $this->categorys = $categorys;
        $this->fields = F("Model_field_" . $modelid);
    }

    /**
     * 获取模型字段信息
     * @param type $data
     * @return type 
     */
    function get($data = array()) {
        //信息ID
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->data = $data;
        $info = array();

        foreach ($this->fields as $field => $v) {
            //判断是否后台
            if (defined('IN_ADMIN') && IN_ADMIN) {
                //判断是否内部字段，如果是，跳过
                if ($v['iscore']) {
                    continue;
                }
            } else {
                //判断是否内部字段或者，是否禁止前台投稿字段
                if ($v['iscore']) {
                    continue;
                }
                if (!$v['isadd']) {
                    continue;
                }
            }
            $func = $v['formtype'];
            $value = isset($data[$field]) ? Input::getVar($data[$field]) : '';
            if ($func == 'pages' && isset($data['maxcharperpage'])) {
                $value = $data['paginationtype'] . '|' . $data['maxcharperpage'];
            }
            //判断对应方法是否存在，不存在跳出本次循环
            if (!method_exists($this, $func)){
                continue;
            }
            //传入参数 字段名 字段值 字段信息
            $form = $this->$func($field, $value, $v);
            if ($form !== false) {
                //作为基本信息
                if ($v['isbase']) {
                    $star = $v['minlength'] || $v['pattern'] ? 1 : 0;
                    $info['base'][$field] = array('name' => $v['name'], 'tips' => $v['tips'], 'form' => $form, 'star' => $star, 'isomnipotent' => $v['isomnipotent'], 'formtype' => $v['formtype']);
                } else {
                    $star = $v['minlength'] || $v['pattern'] ? 1 : 0;
                    $info['senior'][$field] = array('name' => $v['name'], 'tips' => $v['tips'], 'form' => $form, 'star' => $star, 'isomnipotent' => $v['isomnipotent'], 'formtype' => $v['formtype']);
                }
            }
        }

        //配合 validate 插件，生成对应的js验证规则
        $this->formValidateRules = $this->ValidateRulesJson($this->formValidateRules);
        $this->formValidateMessages = $this->ValidateRulesJson($this->formValidateMessages, true);
        return $info;
    }

    /**
     * 转换为validate表单验证相关的json数据
     * @param type $ValidateRules
     */
    public function ValidateRulesJson($ValidateRules, $suang = false) {
        foreach ($ValidateRules as $formname => $value) {
            $va = array();
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    //如果作为消息，消息内容需要加引号，不然会JS报错，是否验证不需要
                    if ($suang) {
                        $va[] = "$k:'$v'";
                    } else {
                        $va[] = "$k:$v";
                    }
                }
            }
            $va = "{" . implode(",", $va) . "}";
            $formValidateRules[] = "'$formname':$va";
        }
        $formValidateRules = "{" . implode(",", $formValidateRules) . "}";
        return $formValidateRules;
    }




//文本框
function text($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);
    $size = $setting['size'];
    if (!$value)
        $value = $defaultvalue;
    $type = $ispassword ? 'password' : 'text';
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    return '<input type="text" name="info[' . $field . ']" id="' . $field . '" size="' . $size . '" value="' . $value . '" class="input" ' . $formattribute . ' ' . $css . '>';
}



//多行文本框
function textarea($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);
    extract($setting);
    if (!$value)
        $value = $defaultvalue;
    $allow_empty = 'empty:true,';
    if ($minlength || $pattern)
        $allow_empty = '';
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    //如果内容为空，着使用默认值
    $value = empty($value) ? $setting['defaultvalue'] : $value;
    $str = "<textarea name='info[{$field}]' id='$field' style='width:{$width}%;height:{$height}px;' $formattribute $css";
    if ($maxlength)
        $str .= " onkeyup=\"strlen_verify(this, '{$field}_len', {$maxlength})\"";
    $str .= ">{$value}</textarea>";
    if ($maxlength)
        $str .= '还可以输入<B><span id="' . $field . '_len">' . $maxlength . '</span></B>个字符！ ';

    return $str;
}



//编辑器字段
function editor($field, $value, $fieldinfo) {
    $setting = unserialize($fieldinfo['setting']);
    
    extract($setting);
    extract($fieldinfo);

    $disabled_page = isset($disabled_page) ? $disabled_page : 0;
    //编辑器高度
    if (!$height)
        $height = 300;
    //是否允许上传
    if(defined('IN_ADMIN') && IN_ADMIN){
        $allowupload = 1;
    }else{
        $Member_group = F("Member_group");
        $groupid = SiteCookie('groupid');
        $allowupload = $Member_group[$groupid]['allowattachment']?1:0;
        $toolbar = $mbtoolbar?$mbtoolbar:"basic";
    }
    
    //内容
    if (!$value)
        $value = $defaultvalue;
    if ($minlength || $pattern)
        $allow_empty = '';

    $form = Form::editor($field, $toolbar, 'Contents', $this->catid, $allowupload, 1, '',10, $height, $disabled_page);
    //javascript
    $this->formJavascript .= "
            //编辑器
            editor$field = new baidu.editor.ui.Editor(editor_config_$field);
            editor$field.render( '$field' );
            try{editor$field.sync();}catch(err){};
            //增加编辑器验证规则
            jQuery.validator.addMethod('editor$field',function(){
                try{editor$field.sync();}catch(err){};
                return editor$field.hasContents();
            });
    ";
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("editor$field"=>"true");
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("editor$field"=>$errortips?$errortips:$name."不能为空！");
    }
    return "<div id='{$field}_tip'></div>" . '<textarea id="' . $field . '" name="info[' . $field . ']">' . $value . '</textarea>' . $form;
}


/*
 * 栏目字段类型
 */
function catid($field, $value, $fieldinfo) {
    if (!$value)
        $value = $this->catid;
    $publish_str = '';
    if (ACTION_NAME == 'add' && defined("IN_ADMIN") && IN_ADMIN){
        $publish_str = " <a href='javascript:;' onclick=\"omnipotent('selectid','".U("Contents/Content/add_othors",array("catid"=>$this->catid))."','同时发布到其他栏目',1);return false;\" style='color:#B5BFBB'>[同时发布到其他栏目]</a>
            <ul class='three_list cc' id='add_othors_text'></ul>";
    }
    return '<input type="hidden" name="info[' . $field . ']" value="' . $value . '">' . $this->categorys[$value]['catname'] . $publish_str;
}



//标题字段
function title($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $style_arr = explode(';', $this->data['style']);
    $style_color = $style_arr[0];
    $style_font_weight = $style_arr[1] ? $style_arr[1] : '';

    $style = 'color:' . $this->data['style'];
    if (!$value)
        $value = $defaultvalue;
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:"标题不能为空！");
    }
    $str = '<input type="text" style="width:400px;' . ($style_color ? 'color:' . $style_color . ';' : '') . ($style_font_weight ? 'font-weight:' . $style_font_weight . ';' : '') . '" name="info[' . $field . ']" id="' . $field . '" value="' . $value . '" style="' . $style . '" class="input input_hd J_title_color" placeholder="请输入标题" onkeyup="strlen_verify(this, \''.$field.'_len\', '.$maxlength.')" />
                <input type="hidden" name="style_font_weight" id="style_font_weight" value="' . $style_font_weight . '">';
    if (defined('IN_ADMIN') && IN_ADMIN)
        $str .= '<input type="button" class="btn" id="check_title_alt" value="标题检测" onclick="$.get(\''.CONFIG_SITEURL_MODEL.'index.php?a=public_check_title&m=Content&g=Contents&catid=' . $this->catid . '&sid=\'+Math.random()*5, {data:$(\'#title\').val()}, function(data){if(data.status==false) {$(\'#check_title_alt\').val(\'标题重复\');$(\'#check_title_alt\').css(\'background-color\',\'#FFCC66\');} else if(data.status==true) {$(\'#check_title_alt\').val(\'标题不重复\');$(\'#check_title_alt\').css(\'background-color\',\'#F8FFE1\')}},\'json\')" style="width:73px;"/>
                    <span class="color_pick J_color_pick"><em style="background:' . $style_color . ';" class="J_bg"></em></span><input type="hidden" name="style_color" id="style_color" class="J_hidden_color" value="' . $style_color . '">
                    <img src="' . CONFIG_SITEURL_MODEL . 'statics/images/icon/bold.png" width="10" height="10" onclick="input_font_bold()" style="cursor:hand"/>';
    $str .= ' 还可输入<B><span id="title_len">' . $maxlength . '</span></B> 个字符';
    return $str;
}



function box($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    $setting = unserialize($fieldinfo['setting']);
    if ($value == '')
        $value = $setting['defaultvalue'];
    $options = explode("\n", $setting['options']);
    foreach ($options as $_k) {
        $v = explode("|", $_k);
        $k = trim($v[1]);
        $option[$k] = $v[0];
    }
    $values = explode(',', $value);
    $value = array();
    foreach ($values as $_k) {
        if ($_k != '')
            $value[] = $_k;
    }
    $value = implode(',', $value);
    switch ($setting['boxtype']) {
        case 'radio':
            $string = Form::radio($option, $value, "name='info[$field]' $fieldinfo[formattribute]", $setting['width'], $field);
            break;

        case 'checkbox':
            $string = Form::checkbox($option, $value, "name='info[$field][]' $fieldinfo[formattribute]", 1, $setting['width'], $field);
            break;

        case 'select':
            $string = Form::select($option, $value, "name='info[$field]' id='$field' $fieldinfo[formattribute]");
            break;

        case 'multiple':
            $string = Form::select($option, $value, "name='info[$field][]' id='$field ' size=2 multiple='multiple' style='height:60px;' $fieldinfo[formattribute]");
            break;
    }
    return $string;
}



//缩略图
function image($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    $setting = unserialize($fieldinfo['setting']);
    extract($setting);
    $html = '';
    //图片裁减功能只在后台使用
    if (defined('IN_ADMIN') && IN_ADMIN) {
        $html = "<input type=\"button\" class=\"btn\" onclick=\"crop_cut_" . $field . "($('#$field').val());return false;\" value=\"裁减图片\"> 
            <input type=\"button\"  class=\"btn\" onclick=\"$('#" . $field . "_preview').attr('src','" . CONFIG_SITEURL_MODEL . "statics/images/icon/upload-pic.png');$('#" . $field . "').val('');return false;\" value=\"取消图片\"><script type=\"text/javascript\">
            function crop_cut_" . $field . "(id){
	if ( id =='' || id == undefined ) { 
                      isalert('请先上传缩略图！');
                      return false;
                    }
                    var catid = $('input[name=\"info[catid]\"]').val();
                    if(catid == '' ){
                        isalert('请选择栏目ID！');
                        return false;
                    }
                    Wind.use('artDialog','iframeTools',function(){
                      art.dialog.open(GV.DIMAUB+'index.php?a=public_imagescrop&m=Content&g=Contents&catid='+catid+'&picurl='+encodeURIComponent(id)+'&input=$field&preview=" . ($show_type && defined('IN_ADMIN') ? $field . "_preview" : '') . "', {
                        title:'裁减图片', 
                        id:'crop',
                        ok: function () {
                            var iframe = this.iframe.contentWindow;
                            if (!iframe.document.body) {
                                 alert('iframe还没加载完毕呢');
                                 return false;
                            }
                            iframe.uploadfile();
                            return false;
                        },
                        cancel: true
                      });
                    });
            };
</script>";
    }
    //生成上传附件验证
    $authkey = upload_key("1,$upload_allowext,$isselectimage,$images_width,$images_height,$watermark");
    if ($show_type && defined('IN_ADMIN') && IN_ADMIN) {
        $preview_img = $value ? $value : CONFIG_SITEURL_MODEL . 'statics/images/icon/upload-pic.png';
        return $str . "<div  style=\"text-align: center;\"><input type='hidden' name='info[$field]' id='$field' value='$value'>
			<a href='javascript:void(0);' onclick=\"flashupload('{$field}_images', '附件上传','{$field}',thumb_images,'1,{$upload_allowext},$isselectimage,$images_width,$images_height,$watermark','content','$this->catid','$authkey');return false;\">
			<img src='$preview_img' id='{$field}_preview' width='135' height='113' style='cursor:hand' /></a>" . $html . "</div>";
    } else {
        return $str . "<input type='text' name='info[$field]' id='$field' value='$value' size='$size' class='input' />  <input type='button' class='button' onclick=\"flashupload('{$field}_images', '附件上传','{$field}',submit_images,'1,{$upload_allowext},$isselectimage,$images_width,$images_height,$watermark','content','$this->catid','$authkey')\"/ value='上传图片'>" . $html;
    }
}



function images($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    $setting = unserialize($fieldinfo['setting']);
    extract($setting);
    $list_str = '';
    if ($value) {
        $value = unserialize(html_entity_decode($value, ENT_QUOTES));
        if (is_array($value)) {
            foreach ($value as $_k => $_v) {
                $list_str .= "<div id='image_{$field}_{$_k}' style='padding:1px'><input type='text' name='{$field}_url[]' value='{$_v[url]}' style='width:310px;' ondblclick='image_priview(this.value);' class='input'> <input type='text' name='{$field}_alt[]' value='{$_v[alt]}' style='width:160px;' class='input'> <a href=\"javascript:remove_div('image_{$field}_{$_k}')\">移除</a></div>";
            }
        }
    } else {
        $list_str .= "<center><div class='onShow' id='nameTip'>您最多可以同时上传 <font color='red'>{$upload_number}</font>张</div></center>";
    }
    $string = '<input name="info[' . $field . ']" type="hidden" value="1">
		<fieldset class="blue pad-10">
        <legend>图片列表</legend>';
    $string .= $list_str;
    $string .= '<div id="' . $field . '" class="picList"></div>
		</fieldset>
		<div class="bk10"></div>
		';
    //生成上传附件验证
    $authkey = upload_key("$upload_number,$upload_allowext,$isselectimage");
    $string .= $str . "<a herf='javascript:void(0);' onclick=\"javascript:flashupload('{$field}_images', '图片上传','{$field}',change_images,'{$upload_number},{$upload_allowext},{$isselectimage}','content','$this->catid','{$authkey}')\" class=\"btn\"><span class=\"add\"></span>选择图片 </a>";
    return $string;
}



//数字
function number($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);
    $size = $setting['size'];
    if (!$value)
        $value = $defaultvalue;
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    return "<input type='text' name='info[$field]' id='$field' value='$value' class='input' size='$size' {$formattribute} {$css}>";
}


//更新时间 
function datetime($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    extract(unserialize($fieldinfo['setting']));
    $isdatetime = 0;
    $timesystem = 0;
    //时间格式
    if ($fieldtype == 'int') {//整数 显示格式
        if (!$value && $defaulttype)
            $value = time();
        //整数 显示格式
        $format_txt = $format == 'm-d' ? 'm-d' : $format;
        if ($format == 'Y-m-d Ah:i:s')
            $format_txt = 'Y-m-d h:i:s';
        $value = date($format_txt, $value);

        $isdatetime = strlen($format) > 6 ? 1 : 0;
        if ($format == 'Y-m-d Ah:i:s') {

            $timesystem = 0;
        } else {
            $timesystem = 1;
        }
    } elseif ($fieldtype == 'datetime') {
        $isdatetime = 1;
        $timesystem = 1;
    } elseif ($fieldtype == 'datetime_a') {
        $isdatetime = 1;
        $timesystem = 0;
    }
    return Form::date("info[$field]",$value,$isdatetime,1,'true',$timesystem);
}


/**
 *关键字类型字段
 * @param type $field
 * @param type $value
 * @param type $fieldinfo
 * @return type 
 */
function keyword($field, $value, $fieldinfo) {
    extract($fieldinfo);
    if (!$value)
        $value = $defaultvalue;
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:"请输入关键字！");
    }
    return "<input type='text' name='info[$field]' id='$field' value='$value' style='width:280px' {$formattribute} {$css} class='input' placeholder='请输入关键字'>";
}



function author($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    return '<input type="text" class="input" name="info[' . $field . ']" value="' . $value . '" size="30">';
}



//来源字段
function copyfrom($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    return "<input type='text' name='info[$field]' value='$value' style='width: 400px;' class='input' placeholder='信息来源'>";
}


//转向地址
function islink($field, $value, $fieldinfo) {
    if ($value) {
        $url = $this->data['url'];
        $checked = 'checked';
        $_GET['islink'] = 1;
    } else {
        $disabled = 'disabled';
        $url = $checked = '';
        $_GET['islink'] = 0;
    }
    $size = $fieldinfo['size'] ? $fieldinfo['size'] : 25;
    return '<input type="hidden" name="info[islink]" value="0"><input type="text" name="linkurl" id="linkurl" value="' . $url . '" size="' . $size . '" maxlength="255" ' . $disabled . ' class="input length_3"> <input name="info[islink]" type="checkbox" id="islink" value="1" onclick="ruselinkurl();" ' . $checked . '> <font color="red">转向链接</font>';
}



//模板字段
function template($field, $value, $fieldinfo) {
    return Form::select_template("", 'content', $value, 'name="info[' . $field . ']" id="' . $field . '"', 'show');
}



//分页选择字段
function pages($field, $value, $fieldinfo) {
    extract($fieldinfo);
    if ($value) {
        $v = explode('|', $value);
        $data = "<select name=\"info[paginationtype]\" id=\"paginationtype\" onchange=\"if(this.value==1)\$('#paginationtype1').css('display','');else \$('#paginationtype1').css('display','none');\">";
        $type = array("不分页", "自动分页", "手动分页");
        if ($v[0] == 1)
            $con = 'style="display:"';
        else
            $con = 'style="display:none"';
        foreach ($type as $i => $val) {
            if ($i == $v[0])
                $tag = 'selected';
            else
                $tag = '';
            $data .= "<option value=\"$i\" $tag>$val</option>";
        }
        $data .= "</select><span id=\"paginationtype1\" $con> <input name=\"info[maxcharperpage]\" type=\"text\" id=\"maxcharperpage\" value=\"$v[1]\" size=\"8\" maxlength=\"8\" class='input'>字符数（包含HTML标记）</span>";
        return $data;
    } else {
        return "<select name=\"info[paginationtype]\" id=\"paginationtype\" onchange=\"if(this.value==1)\$('#paginationtype1').css('display','');else \$('#paginationtype1').css('display','none');\">
                <option value=\"0\">不分页</option>
                <option value=\"1\" selected>自动分页</option>
                <option value=\"2\">手动分页</option>
            </select>
			<span id=\"paginationtype1\" style=\"\"><input name=\"info[maxcharperpage]\" type=\"text\" id=\"maxcharperpage\" value=\"10000\" size=\"8\" maxlength=\"8\" class='input'>字符数（包含HTML标记）</span>";
    }
}


/**
 *类别字段类型
 * @param type $field
 * @param type $value
 * @param type $fieldinfo
 * @return type 
 */
function typeid($field, $value, $fieldinfo) {
    return $value;
}


//推荐位
function posid($field, $value, $fieldinfo) {
    $setting = unserialize($fieldinfo['setting']);
    //推荐位缓存
    $position = F("Position");
    if (empty($position))
        return '';
    $array = array();
    foreach ($position as $_key => $_value) {
        if ($_value['modelid'] && ($_value['modelid'] != $this->modelid) || ($_value['catid'] && strpos(',' . $this->categorys[$_value['catid']]['arrchildid'] . ',', ',' . $this->catid . ',') === false))
            continue;
        $array[$_key] = $_value['name'];
    }
    $posids = array();
    if (ACTION_NAME == 'edit') {
        $this->position_data_db = M('Position_data');
        $result = $this->position_data_db->where(array('id' => $this->id, 'modelid' => $this->modelid))->getField("posid,id,catid,posid,module,modelid,thumb,data,listorder,expiration,extention,synedit");
        $posids = implode(',', array_keys($result));
    } else {
        $posids = $setting['defaultvalue'];
    }
    return "<input type='hidden' name='info[$field][]' value='-1'>" . Form::checkbox($array, $posids, "name='info[$field][]'", '', $setting['width']);
}



function linkfield($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);

    if ($setting['link_type']) {

        $get_db = M("");

        $sel_tit = $setting['select_title'] ? $setting['select_title'] : '*';

        $sql = "SELECT " . $sel_tit . " FROM `" . $setting['table_name'] . "`";

        $dataArr = $get_db->query($sql);

        $value = str_replace('&amp;', '&', $value);
        $data = '<select name="info[' . $fieldinfo['field'] . ']" id="' . $fieldinfo['field'] . '"><option>请选择</option>';

        foreach ($dataArr as $v) {
            if ($setting['insert_type'] == "id") {
                $output_type = $v[$setting['set_id']];
            } elseif ($setting['insert_type'] == "title") {
                $output_type = $v[$setting['set_title']];
            } else {
                $output_type = $v[$setting['set_title']] . '_' . $v[$setting['set_id']];
            }
            if ($output_type == $value)
                $select = 'selected';
            else
                $select = '';
            $data .= "<option value='" . $output_type . "' " . $select . ">" . $v[$setting['set_title']] . "</option>\n";
        }
        $data .= '</select>';
    }else {
        $key = urlencode(authcode("true","",C("AUTHCODE"),3600));
        $domain = CONFIG_SITEURL_MODEL;
        $data = <<<EOT
            <style type="text/css">
            .content_div{ margin-top:0px; font-size:14px; position:relative}
            #search_div{$field}{ position:absolute; top:23px; border:1px solid #dfdfdf; text-align:left; padding:1px; left:0px;*left:0px; width:263px;*width:260px; background-color:#FFF; display:none; font-size:12px;}
            #search_div{$field} li{ line-height:24px;cursor:pointer}
            #search_div{$field} li a{  padding-left:6px;display:block}
            #search_div{$field} li a:hover, #search_div{$field} li:hover{ background-color:#e2eaff}
            </style>
            <div class="content_div">
                <input type="text" size="41" id="cat_search{$field}" value="" onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;" class='input'><input name="info[{$fieldinfo['field']}]" id="{$fieldinfo['field']}" type="hidden" class='input' value="{$value}" size="41"/>
                <ul id="search_div{$field}"></ul>
            </div>		
            <script type="text/javascript" language="javascript" >
                function setvalue{$field}(title,id)
                {
                    var title = title;
                    var id = id;
                    var type = "{$setting['insert_type']}";
                    if(type == "id")
                    {
                        $("#{$fieldinfo['field']}").val(id);
                    }
                    else if(type == "title")
                    {
                        $("#{$fieldinfo['field']}").val(title);
                    }
                    else if(type == "title_id")
                    {
                        $("#{$fieldinfo['field']}").val(title+'|'+id);
                    }
                    $("#cat_search{$field}").val(title);
                    $('#search_div{$field}').hide();
                }
				
            $(document).ready(function(){
				if($("#{$fieldinfo['field']}").val().length > 0){
				
					var value = $("#{$fieldinfo['field']}").val();
					var tablename = '{$setting['table_name']}';
					var set_title = '{$setting['set_title']}';
					var set_id = '{$setting['set_id']}';
					var set_type = '{$setting['insert_type']}';
					$.getJSON('{$domain}api.php?m=Ajax_linkfield&a=public_index&act=check_search&key={$key}&callback=?', {value: value,table_name: tablename,set_title: set_title,set_id: set_id,set_type: set_type,random:Math.random()}, function(data2){
						if (data2 != null) {
							$.each(data2, function(i,n){				
							$('#cat_search{$field}').val(n.{$setting['set_title']});
							});
						} else {
							$('#search_div{$field}').hide();
						}
					});
					
				}

				$('#cat_search{$field}').keyup(function(){
					var value = $("#cat_search{$field}").val();
					var tablename = '{$setting['table_name']}';
					var select_title = '{$setting['select_title']}';
					var like_title = '{$setting['like_title']}';
					var set_title = '{$setting['set_title']}';
					var set_id = '{$setting['set_id']}';
					
					if (value.length > 0){
						$.getJSON('{$domain}api.php?m=Ajax_linkfield&a=public_index&act=search_ajax&key={$key}&callback=?', {value: value,table_name: tablename,select_title: select_title,like_title: like_title,set_title: set_title,set_id: set_id,limit: 20,random:Math.random()}, function(data){
							if (data != null) {
								var str = '';
								$.each(data, function(i,n){
									str += '<li onclick=\'setvalue{$field}("'+n.{$setting['set_title']}+'","'+n.{$setting['set_id']}+'");\'>'+n.{$setting['set_title']}+'</li>';
								});
								$('#search_div{$field}').html(str);
								$('#search_div{$field}').show();
							} else {
								$('#search_div{$field}').hide();
							}
						});
					} else {
						$('#search_div{$field}').hide();
					} 
				});	
            })
            </script>
EOT;
    }
    return $data;
}



//多文件上传
function downfiles($field, $value, $fieldinfo) {
    extract($fieldinfo);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    extract(unserialize($fieldinfo['setting']));
    $list_str = '';
    if ($value) {
        $value = unserialize(html_entity_decode($value, ENT_QUOTES));
        if (defined("IN_ADMIN") && IN_ADMIN) {
            import("Form");
            $Member_group = F("Member_group");
            foreach ($Member_group as $v) {
                if (in_array($v['groupid'], array("1", "7","8"))) {
                    continue;
                }
                $group[$v['groupid']] = $v['name'];
            }
        }
        if (is_array($value)) {
            foreach ($value as $_k => $_v) {
                if (defined("IN_ADMIN") && IN_ADMIN) {
                    $list_str .= "<div id='multifile{$_k}'><input type='text' name='{$field}_fileurl[]' value='{$_v[fileurl]}' style='width:310px;' class='input'> <input type='text' name='{$field}_filename[]' value='{$_v[filename]}' style='width:160px;' class='input'> 权限：" . Form::select($group, $_v['groupid'], 'name="' . $field . '_groupid[]"', '游客') . " 点数：<input type='text' name='{$field}_point[]' value='" . $_v['point'] . "' style='width:60px;' class='input'> <a href=\"javascript:remove_div('multifile{$_k}')\">移除</a></div>";
                } else {
                    $list_str .= "<div id='multifile{$_k}'><input type='text' name='{$field}_fileurl[]' value='{$_v[fileurl]}' style='width:310px;' class='input'> <input type='text' name='{$field}_filename[]' value='{$_v[filename]}' style='width:160px;' class='input'> <a href=\"javascript:remove_div('multifile{$_k}')\">移除</a></div>";
                }
            }
        }
    }
    $string = '<input name="info[' . $field . ']" type="hidden" value="1">
		<fieldset class="blue pad-10">
        <legend>文件列表</legend>';
    $string .= $list_str;
    $string .= '<ul id="' . $field . '" class="picList"></ul>
		</fieldset>
		<div class="bk10"></div>
		';

    //生成上传附件验证
    $authkey = upload_key("$upload_number,$upload_allowext,$isselectimage");
    //后台允许权限设置
    if (defined("IN_ADMIN") && IN_ADMIN) {
        import("Form");
        $Member_group = F("Member_group");
        foreach ($Member_group as $v) {
            if (in_array($v['groupid'], array("1", "7","8"))) {
                continue;
            }
            $group[$v['groupid']] = $v['name'];
        }
        $js = '<script type="text/javascript">
function change_multifile_admin(uploadid, returnid) {
    var d = uploadid.iframe.contentWindow;
    var in_content = d.$("#att-status").html().substring(1);
    var in_filename = d.$("#att-name").html().substring(1);
    var str = \'\';
    var contents = in_content.split(\'|\');
    var filenames = in_filename.split(\'|\');
    var group = \'权限：' . Form::select($group, $id, 'name="\' + returnid + \'_groupid[]"', '游客') . '\';
    $(\'#\' + returnid + \'_tips\').css(\'display\', \'none\');
    if (contents == \'\') return true;
    $.each(contents, function (i, n) {
        var ids = parseInt(Math.random() * 10000 + 10 * i);
        var filename = filenames[i].substr(0, filenames[i].indexOf(\'.\'));
        str += "<li id=\'multifile" + ids + "\'><input type=\'text\' name=\'" + returnid + "_fileurl[]\' value=\'" + n + "\' style=\'width:310px;\' class=\'input\'> <input type=\'text\' name=\'" + returnid + "_filename[]\' value=\'" + filename + "\' style=\'width:160px;\' class=\'input\' onfocus=\"if(this.value == this.defaultValue) this.value = \'\'\" onblur=\"if(this.value.replace(\' \',\'\') == \'\') this.value = this.defaultValue;\"> "+group+" 点数：<input type=\'text\' name=\'" + returnid + "_point[]\' value=\'0\' style=\'width:60px;\' class=\'input\'> <a href=\"javascript:remove_div(\'multifile" + ids + "\')\">移除</a> </li>";
    });
    $(\'#\' + returnid).append(str);
}

function add_multifile_admin(returnid) {
    var ids = parseInt(Math.random() * 10000);
    var group = \'权限：' . Form::select($group, $id, 'name="\' + returnid + \'_groupid[]"', '游客') . '\';
    var str = "<li id=\'multifile" + ids + "\'><input type=\'text\' name=\'" + returnid + "_fileurl[]\' value=\'\' style=\'width:310px;\' class=\'input\'> <input type=\'text\' name=\'" + returnid + "_filename[]\' value=\'附件说明\' style=\'width:160px;\' class=\'input\'> "+group+"  点数：<input type=\'text\' name=\'" + returnid + "_point[]\' value=\'0\' style=\'width:60px;\' class=\'input\'>  <a href=\"javascript:remove_div(\'multifile" + ids + "\')\">移除</a> </li>";
    $(\'#\' + returnid).append(str);
};</script>';
        $string .= $str . "<a herf='javascript:void(0);' class=\"btn\"  onclick=\"javascript:flashupload('{$field}_multifile', '附件上传','{$field}',change_multifile_admin,'{$upload_number},{$upload_allowext},{$isselectimage}','content','$this->catid','{$authkey}')\"><span class=\"add\"></span>多文件上传</a>    <a  class=\"btn\" herf='javascript:void(0);'  onclick=\"add_multifile_admin('{$field}')\"><span class=\"add\"></span>添加远程地址</a>$js";
    } else {
        $string .= $str . "<a herf='javascript:void(0);'  class=\"btn\" onclick=\"javascript:flashupload('{$field}_multifile', '附件上传','{$field}',change_multifile,'{$upload_number},{$upload_allowext},{$isselectimage}','content','$this->catid','{$authkey}')\"><span class=\"add\"></span>多文件上传</a>    <a herf='javascript:void(0);' class=\"btn\" onclick=\"add_multifile('{$field}')\"><span class=\"add\"></span>添加远程地址</a>";
    }
    return $string;
}



function map($field, $value, $fieldinfo) {
    $setting = unserialize($fieldinfo['setting']);
    extract($setting);
    if ($value) {
        $value = explode("|", $value);
        $mapx = $value[0];
        $mapy = $value[1];
        if ($mapclass == 1)
            $mapz = $value[2];
    }
    $data = "<input type='text' size='20' name='info[" . $field . "][mapx]' id='" . $field . "mapx' value='" . $mapx . "' class='input' />&nbsp;&nbsp;&nbsp;<input type='text' size='20' name='info[" . $field . "][mapy]' id='" . $field . "mapy' value='" . $mapy . "' class='input' />";
    if ($mapclass == 1) {
        $mapjs = <<< herf
		<script language="javascript" src="http://api.51ditu.com/js/maps.js"></script>
        <script language="javascript" src="http://api.51ditu.com/js/ezmarker.js"></script>
		<script language="JavaScript">
		<!--
        //setMap是ezmarker内部定义的接口，这里可以根据实际需要实现该接口
        function setMap(point,zoom){
			document.getElementById("{$field}mapx").value=point.getLongitude();
			document.getElementById("{$field}mapy").value=point.getLatitude();
            document.getElementById("{$field}mapz").value=zoom;
        }
        var ezmarker = new LTEZMarker("{$field}");	
		//var c = "huzhou";
		//ezmarker.setDefaultView(c,3);
		ezmarker.setValue(new LTPoint({$mapx},{$mapy}),{$mapz});
        LTEvent.addListener(ezmarker,"mark",setMap);//"mark"是标注事件
        //-->
        </script>
herf;

        $data .="&nbsp;&nbsp;&nbsp;<input type='text' size='20' name='info[" . $field . "][mapz]' id='" . $field . "mapz' value='" . $mapz . "'/>" . $mapjs;
    } else if ($mapclass == 2) {
        $mapjs = <<< herf
			<div id="{$field}mapdiv" style="position:absolute; top:200px; left:200px; width: 500px; height: 500px; background-color:#F2EFE9; text-align:center;display:none;"><div id='{$field}mapmark_mymap' style='width: 500px; height: 500px;' ></div>
            <br/>
            <input type="button" value="添加标注" onclick="setp_{$field}()"  class='btn' /> <input type="button" id="findp" value="跳到标记处"  onclick="find_{$field}()"  class='btn' /> <input type="button" value="关闭窗口" onclick="$('#{$field}mapdiv').hide();"  class='btn'  />
            </div>
			<script type = "text/javascript" src ="http://union.mapbar.com/apis/maps/free?f=mapi&v=31.2&k={$mapz}"></script> 
<script type="text/javascript"> 
var maplet=null;//地图对象 
var marker_{$field}=null;//标记对象 
var le=null;//缩放级别 
var myEventListener_{$field}=null;//地图click事件句柄 
function initMap_{$field}()//初始化函数 
{  
le=10; //默认缩放级别 
maplet = new Maplet("{$field}mapmark_mymap"); 
//这里可以初始化地图坐标比如从数据库中读取 然后在页面上使用小脚本的形式 
//如: maplet.centerAndZoom(new MPoint(<%=经度%>,<%=维度%> ),<%=缩放级别%>); 
maplet.centerAndZoom(new MPoint({$mapy}, {$mapx}), le);//初始化地图中心点坐标并设定缩放级别 
maplet.addControl(new MStandardControl()); 
} 
function setp_{$field}() 
{ 
if(marker_{$field}){
	maplet.removeOverlay(marker_{$field});
}
maplet.setMode("{$field}bookmark");//设定为添加标记模式 
maplet.setCursorIcon("/statics/images/tack.gif"); //添加鼠标跟随标签 
myEventListener_{$field} = MEvent.bind(maplet, "click", this, addp_{$field}); //注册click事件句柄 
} 
//这里的参数要写全即使你不使用event 
function addp_{$field}(event,point){
//removeMarker_{$field}();
marker_{$field} = new MMarker( point, new MIcon("/statics/images/mapbar_ok_tack.gif", 78, 78)); 
marker_{$field}.bEditable=true; 
marker_{$field}.dragAnimation=true; 
maplet.addOverlay(marker_{$field});//添加标注 
marker_{$field}.setEditable(true); //设定标注编辑状态 
maplet.setMode("pan"); //设定地图为拖动(正常)状态 
le= maplet.getZoomLevel(); //获取当前缩放级别 
 
document.getElementById("{$field}mapx").value=marker_{$field}.pt.lat; 
document.getElementById("{$field}mapy").value=marker_{$field}.pt.lon;
//document.getElementById("{$field}mapz").value=le;
MEvent.removeListener(myEventListener_{$field});//注销事件

MEvent.addListener(maplet, "edit", dragEnd); 
} 
//查找标记 
function find_{$field}(){ 
maplet.centerAndZoom(marker_{$field}.pt, le);//定位标记 
}
function dragEnd_{$field}(overlay){   
	setTimeout(function(){ 
		document.getElementById("{$field}mapx").value=overlay.pt.lat;
		document.getElementById("{$field}mapy").value=overlay.pt.lon; 
		//document.getElementById("{$field}mapz").value=maplet.getZoomLevel();    
        //overlay.setEditable(false);   
        },500);   
}
function removeMarker_{$field}(){   
	var selector = document.getElementById("{$field}mapmark_mymap");   
	var item = selector.options[selector.selectedIndex].value;   
	maplet.removeOverlay(markerArr[item]);   
	selector.removeChild(selector.options[selector.selectedIndex]);      
}   


initMap_{$field}();
</script>
<a onclick="$('#{$field}mapdiv').show();" /><img src="/statics/images/button-f.gif"></a>
herf;
        $data .=$mapjs;
    } else {
        $mapjs = <<< herf
			
			<div id="{$field}mapdiv" style="position:absolute; right:100px;width: 505px; height: 400px; background-color:#F2EFE9; text-align:center;display:none;"><div id='{$field}mark_mymap' style='width: 505px; height: 350px;' ></div>
            <br/>
            <input type="button" value="关闭地图窗口" onClick="$('#{$field}mapdiv').hide();" />
            </div>

			<script src="http://maps.google.com/maps?file=api&v=2&key={$mapz}" type="text/javascript" charset="utf-8"></script>
		   <script type="text/javascript" language="javascript">
		   function atmark_{$field}() { //标注接口开始
				var map = null;
				if (GBrowserIsCompatible()) { //判断是否生成
					var map = new GMap2(document.getElementById('{$field}mark_mymap'));
					map.setCenter(new GLatLng({$mapx},{$mapy}), 14);
					map.addControl(new GSmallMapControl()); //是否显示缩放
					map.addControl(new GMapTypeControl()); //是否显示卫星地图
				}
				map.clearOverlays(marker);   //清除地图上的标记点，否则会显示多个                        
				var Center = map.getCenter();
				var lat = new String(Center.lat());
				var lng = new String(Center.lng());
				setLatLng_{$field}(lat, lng);
				var marker = new GMarker(new GLatLng(lat, lng), {draggable: true});
				GEvent.addListener(marker, "dragend", function() {
				var latlng = marker.getLatLng();
				lat = String(latlng.lat());
				lng = String(latlng.lng());
				setLatLng_{$field}(lat, lng);
			});
			map.addOverlay(marker); // 写入标记到地图上
			}
			function setLatLng_{$field}(lat,lng) {
				document.getElementById("{$field}mapx").value=lat;
				document.getElementById("{$field}mapy").value=lng; 
			}
			</script>
			<a onclick="$('#{$field}mapdiv').show();atmark_{$field}();" /><img src="/statics/images/button-f.gif"></a>
		               
herf;
        $data .=$mapjs;
    }
    return $data;
}



//万能字段
function omnipotent($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);
    $formtext = str_replace('{FIELD_VALUE}', $value, $setting["formtext"]);
    $formtext = str_replace('{MODELID}', $this->modelid, $formtext);
    preg_match_all('/{FUNC\((.*)\)}/', $formtext, $_match);
    foreach ($_match[1] as $key => $match_func) {
        $string = '';
        $params = explode('~~', $match_func);
        $user_func = $params[0];
        $string = $user_func($params[1]);
        $formtext = str_replace($_match[0][$key], $string, $formtext);
    }
    $id = $this->id ? $this->id : 0;
    $formtext = str_replace('{ID}', $id, $formtext);
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    if ($minlength){
        //验证规则
        $this->formValidateRules['info[' . $field . ']']= array("required"=>true);
        //验证不通过提示
        $this->formValidateMessages['info[' . $field . ']']= array("required"=>$errortips?$errortips:$name."不能为空！");
    }
    return $formtext;
}


 } 
?>