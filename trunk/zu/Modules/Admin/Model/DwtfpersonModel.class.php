<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-5-21
 * Time: 下午9:22
 * To change this template use File | Settings | File Templates.
 */
class DwtfpersonModel extends RelationModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '姓名不能为空！', 1, 'regex', 3),
        //array('id', 'require', '供应商不能为空！', 1, 'regex', 3),
    );

    protected  $_map = array(
        'id' =>'wtf_id'
    );

    //关联定义
    protected $_link = array(
        //和角色吧关联，一对一
        'Dwtf_info' => array(
            "mapping_type" =>BELONGS_TO,
            //关联表名
            "class_name" =>"Dwtfmsg",
            "foreign_key" =>"wtf_id",
            "mapping_fields" => 'wtf_id,wtf_name',
            'mapping_name'=>'wtfinfo',
        ),
    );

}
