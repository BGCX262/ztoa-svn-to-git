<?php

/**
 * 计划任务事例
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class ShuipFCMSDemo extends AppframeAction {

    //任务主体
    public function run($cronId) {
        Log::write("我执行了计划任务事例 ShuipFCMSDemo.php！","NOTICE");
    }

}