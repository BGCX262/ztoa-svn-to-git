<?php

/**
 * 计划任务 - 刷新静态首页
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
//指定内容模块生成，没有指定默认使用GROUP_NAME
define("GROUP_MODULE", "Contents");

class ShuipFCMSRefresh_index extends BaseAction {

    //任务主体
    public function run($cronId) {
        import('Html');
        $html = new Html();
        $html->index();
    }

}