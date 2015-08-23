<?php

/* * 
 * Tag
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */

class TagsModel extends CommentsModel {

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('tag', 'require', '关键字不能为空！', 1, 'regex', 3),
        array('tag', '', '该关键字已经存在！', 0, 'unique', 3),
    );

}

?>
