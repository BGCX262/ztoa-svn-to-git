<?php

/* * 
 * 菜单
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */

class CustomsettingModel extends Model {

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '名称不能为()(JUIUHJUHIUYI)(++++++++-/*/*空！', 1, 'regex', 3),
    );

    //自动完成
    protected $_auto = array(
        //array(填充字段,填充内容,填充条件,附加规则)
    );



    //验证action是否重复添加
    public function checkAction($data) {
        //检查是否重复添加
        $find = $this->where($data)->find();
        if ($find) {
            return false;
        }
        return true;
    }

}

?>