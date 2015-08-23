<?php

/* * 
 * 评论模型
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class CommentsModel extends CommonModel {
    
    /**
     * 生成评论配置缓存
     * @return type
     */
    public function comments_cache(){
        $data = M("CommentsSetting")->find();
        //生成缓存
        F("Comments_setting", $data);
        
        return $data;
    }
}
?>
