<?php

/**
 * 推荐位
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class PositionTagLib {

    /**
     * 推荐位数据获取
     * 参数名	 是否必须	 默认值	 说明
     * posid	 是	 null	 推荐位ID
     * catid	 否	 null	 调用栏目ID
     * thumb	 否	 0	 是否仅必须缩略图
     * order	 否	 null	 排序类型
     * num	 是	 null	 数据调用数量
     * @param type $data 
     */
    public function position($data) {
        $posid = (int) $data['posid'];
        if ($posid < 1) {
            return false;
        }
        //缓存处理
        $cache = (int) $data['cache'];
        $cacheID = md5(implode(",", $data));
        $cacheData = S($cacheID);
        if ($cache && $cacheData) {
            return $cacheData;
        }
        $catid = (int) $data['catid'];
        $thumb = isset($data['thumb']) ? $data['thumb'] : 0;
        $order = empty($data['order']) ? array("listorder" => "DESC", "id" => "DESC") : $data['order'];
        $num = (int) $data['num'];

        $db = M("Position_data");
        $Position = F("Position");
        if ($num == 0) {
            $num = $Position[$posid]['maxnum'];
        }
        $where = array();
        $where['posid'] = array("EQ", $posid);
        if ($thumb) {
            $where['thumb'] = array("EQ", 1);
        }
        if ($catid > 0) {
            $where['catid'] = array("EQ", $catid);
        }
        $data = $db->where($where)->order($order)->limit($num)->select();
        $Model = F("Model");
        foreach ($data as $k => $v) {
            $data[$k]['data'] = unserialize($v['data']);
            $tab = ucwords($Model[$v['modelid']]['tablename']);
            $data[$k]['data']['url'] = M($tab)->where(array("id" => $v['id']))->getField("url");
        }
        if ($data) {
            //结果进行缓存
            if ($cache) {
                S($cacheID, $data, $cache);
            }
            return $data;
        } else {
            return false;
        }
    }

}

?>
