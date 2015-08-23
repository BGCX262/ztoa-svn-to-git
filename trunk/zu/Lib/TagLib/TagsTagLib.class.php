<?php

/**
 * Tags标签处理类
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class TagsTagLib {

    public $db;

    /**
     * 统计
     */
    public function count($data) {
        if ($data['action'] == 'lists') {
            $where = array();
            if (isset($data['tagid'])) {
                $where['tagid'] = array("EQ", (int) $data['tagid']);
                $r = M("Tags")->where($where)->find();
                return M("Tags")->where(array("tag" => $r['tag']))->count();
            } else {
                $where['tag'] = array("EQ", $data['tag']);
                return M("Tags_content")->where($where)->count();
            }
        }
    }

    /**
     * 列表（lists）
     * 参数名	 是否必须	 默认值	 说明
     * tag	 否	 null	 tag名称
     * tagid	 否	 null	 tagID
     * num	 否	 10	 返回数量
     * order	 否	 null	 排序类型
     * 
     * @param $data
     */
    public function lists($data) {
        //缓存
        $_key = md5(($data['tagid']?$data['tagid']:$data['tag']).$data['limit']);
        if ((int) $data['cache'] > 0) {
            $return = S("Tags_" . $_key);
            if($return){
                return $return;
            }
        }
        $this->db = M("Tags_content");
        $where = array();
        if (isset($data['tagid'])) {
            $where['tagid'] = array("EQ", (int) $data['tagid']);
            $r = M("Tags")->where($where)->find();
            $where['tag'] = $r['tag'];
        } else {
            $where['tag'] = array("EQ", $data['tag']);
        }
        
        //判断是否启用分页，如果没启用分页则显示指定条数的内容
        if (!isset($data['limit'])) {
            $data['limit'] = (int) $data['num'] == 0 ? 10 : (int) $data['num'];
        }

        //排序
        if (!empty($data['order'])) {
            $return = $this->db->where($where)->order($data['order'])->limit($data['limit'])->select();
        } else {
            $return = $this->db->where($where)->order(array("updatetime" => "DESC"))->limit($data['limit'])->select();
        }

        $Model = F("Model");
        //读取文章信息
        foreach ($return as $k => $v) {
            $tablename = ucwords($Model[$v['modelid']]['tablename']);
            $r = M($tablename)->where(array("id" => $v['contentid']))->find();
            if ($r) {
                $return[$k] = array_merge($v, $r);
            }
        }
         if ((int) $data['cache'] > 0) {
             S("Tags_" . $_key,$return ,(int) $data['cache']);
         }
        return $return;
    }

    /**
     *排行榜 （top） 
     * 参数名	 是否必须	 默认值	 说明
     * num	 否	 10	 返回数量
     * order	 否	 hits DESC	 排序类型
     */
    public function top($data){
        $this->db = M("Tags");
        $num = $data['num']?$data['num']:10;
        //排序
        if (!empty($data['order'])) {
            $return = $this->db->order($data['order'])->limit($num)->select();
        } else {
            $return = $this->db->order(array("hits" => "DESC"))->limit($num)->select();
        }
        return $return;
    }
}

?>
