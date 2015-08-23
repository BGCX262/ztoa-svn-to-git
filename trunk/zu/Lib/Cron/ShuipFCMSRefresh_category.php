<?php

/**
 * 计划任务 - 刷新静态栏目页
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
//指定内容模块生成，没有指定默认使用GROUP_NAME
define("GROUP_MODULE", "Contents");

class ShuipFCMSRefresh_category extends BaseAction {

    //任务主体
    public function run($cronId) {
        import('Html');
        $html = new Html();
        $r = M("Cron")->where(array("cron_id" => $cronId))->find();
        if ($r) {
            $catid = explode(",", $r['data']);
            if (is_array($catid)) {
                foreach ($catid as $cid) {
                    $page = 1;
                    $j = 1;
                    //开始生成列表
                    do {
                        $html->category($cid, $page);
                        $page++;
                        $j++;
                    } while ($j <= $_GET["_PAGES_"]);
                }
            }
        }
    }

}