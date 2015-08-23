<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-5-21
 * Time: 下午9:22
 * To change this template use File | Settings | File Templates.
 */
class DwtfzhmsgModel extends Model
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('wname', 'require', '户名不能为空！', 1, 'regex', 3),
        array('wbank_name', 'require', '开户行不能为空！', 1, 'regex', 3),
        array('wbank_zh', 'require', '帐号不能为空！', 1, 'regex', 3),
    );

    protected  $_map = array(
        'id' =>'wtf_id',
        'per_id' =>'id',
    );

}
