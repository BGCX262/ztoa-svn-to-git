<?php

/**
 * 内容标签
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
class ContentTagLib {

    public $db, $table_name, $category, $model, $modelid;

    function __construct() {
        $this->category = F("Category");
        $this->model = F("Model");
    }

    /**
     * 初始化模型
     * @param $catid
     * @param $tablename
     */
    public function set_modelid($catid, $tablename = "") {
        if ($this->category[$catid]['type'] != 0)
            return false;
        $this->modelid = $this->category[$catid]['modelid'];
        if (empty($tablename)) {
            $tablename = ucwords($this->model[$this->modelid]['tablename']);
        }
        $this->table_name = $tablename;
        $this->db = M($this->table_name);
        if (empty($this->category)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 统计
     */
    public function count($data) {
        if ($data['action'] == 'lists') {
            $catid = intval($data['catid']);
            $where = array();
            if (!$this->set_modelid($catid))
                return false;
            if (isset($data['where'])) {
                $where['_string'] = $data['where'];
            } else {
                if ($this->category[$catid]['child']) {
                    $catids_str = $this->category[$catid]['arrchildid'];
                    $pos = strpos($catids_str, ',') + 1;
                    $catids_str = substr($catids_str, $pos);
                    $where['catid'] = array("IN", $catids_str);
                } else {
                    $where['catid'] = array("EQ", $catid);
                }
            }
            $where['status'] = array("EQ", 99);
            return $this->db->where($where)->count();
        }
    }

    /**
     * 内容列表（lists）
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID
     * where	 否	 null	 sql语句的where部分
     * thumb	 否	 0	 是否仅必须缩略图
     * order	 否	 null	 排序类型
     * num	 是	 null	 数据调用数量
     * moreinfo	 否	 0	 是否调用副表数据 1为是
     * 
     * moreinfo参数属性，本参数表示在返回数据的时候，会把副表中的数据也一起返回。一个内容模型分为2个表，一个主表一个副表，主表中一般是保存了标题、所属栏目等等短小的数据（方便用于索引），而副表则保存了大字段的数据，如内容等数据。在模型管理中新建字段的时候，是允许你选择存入到主表还是副表的（我们推荐的是，把不重要的信息放到副表中）。
     * @param $data
     */
    public function lists($data) {
        //栏目id
        $catid = intval($data['catid']);
        $this->set_modelid($catid);
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = md5(implode(",", $data));
        if ($cache && $return = S($cacheID)) {
            return $return;
        }

        $where = array();
        $where['status'] = array("EQ", 99);
        //如果有where条件
        if (isset($data['where'])) {
            $where['_string'] = $data['where'];
        } else {
            //缩略图
            if(intval($data['thumb']) ){
                $where['thumb'] = array("NEQ", "");
            }
            //如果有子栏目
            if ($this->category[$catid]['child']) {
                $catids_str = $this->category[$catid]['arrchildid'];
                $pos = strpos($catids_str, ',') + 1;
                $catids_str = substr($catids_str, $pos);
                $where['catid'] = array("IN", $catids_str);
            } else {
                $where['catid'] = array("EQ", $catid);
            }
        }
        //判断是否启用分页，如果没启用分页则显示指定条数的内容
        if (!isset($data['limit'])) {
            $data['limit'] = (int) $data['num'] == 0 ? 10 : (int) $data['num'];
        }

        //排序
        if (!empty($data['order'])) {
            $return = $this->db->where($where)->order($data['order'])->limit($data['limit'])->select();
        } else {
            $return = $this->db->where($where)->order(array("updatetime" => "DESC", "id" => "DESC"))->limit($data['limit'])->select();
        }
        $getLastSql .= $this->db->getLastSql()."|";
        //调用副表的数据
        if (isset($data['moreinfo']) && intval($data['moreinfo']) == 1) {
            $ids = array();
            foreach ($return as $v) {
                if (isset($v['id']) && !empty($v['id'])) {
                    $ids[] = $v['id'];
                } else {
                    continue;
                }
            }
            if (!empty($ids)) {
                //从新初始化模型
                $this->set_modelid($catid, $this->table_name . '_data');
                $ids = implode(',', $ids);
                $where = array();
                $where['id'] = array("IN", $ids);
                $r = $this->db->where($where)->select();
                $getLastSql .= $this->db->getLastSql()."|";
                if (!empty($r)) {
                    foreach ($r as $k => $v) {
                        if (isset($return[$k]))
                            $return[$k] = array_merge($v, $return[$k]);
                    }
                }
            }
        }
        //结果进行缓存
        if ($cache) {
            S($cacheID, $return, $cache);
        }
        //log
        if (APP_DEBUG && C('LOG_RECORD')) {
            $msg = "ContentTagLib标签->lists：参数：catid=$catid ,modelid=$modelid ,order=".$data['order']." ,
            SQL:".$getLastSql;
            Log::write($msg);
        }
        return $return;
    }

    /**
     * 排行榜标签
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID，只支持单栏目
     * modelid 否              null              模型ID
     * day	 否	 0	 调用多少天内的排行
     * order	 否	 null	 排序类型（本月排行- monthviews DESC 、本周排行 - weekviews DESC、今日排行 - dayviews DESC）
     * num	 是	 null	 数据调用数量
     * @param $data
     */
    public function hits($data) {
        $catid = intval($data['catid']);
        $modelid = intval($data['modelid']);
        //初始化模型
        if ($modelid) {
            $tablename = ucwords($this->model[$this->modelid]['tablename']);
            $this->table_name = $tablename;
            $this->db = M($this->table_name);
        }elseif($catid){
            $this->set_modelid($catid);
        }else{
            return false;
        }

        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = md5(implode(",", $data));
        if ($cache && $array = S($cacheID)) {
            return $array;
        }

        //点击表
        $this->hits_db = M("Hits");
        $desc = $ids = '';
        $where = $array = $ids_array = array();
        //排序
        $order = $data['order'];
        if (!$order) {
            $order = " views DESC ";
        }
        //条数
        $num = (int) $data['num'];
        if ($num < 1) {
            $num = 10;
        }
        if ($catid) {
            $where['catid'] = array("EQ", $catid);
        }
        //如果调用的栏目是存在子栏目的情况下
        if ($catid && $this->category[$catid]['child']) {
            $catids_str = $this->category[$catid]['arrchildid'];
            $pos = strpos($catids_str, ',') + 1;
            $catids_str = substr($catids_str, $pos);
            $where['catid'] = array("IN", $catids_str);
        }
        if ($modelid) {
            $where['modelid'] = array("EQ", $modelid);
        }

        if (isset($data['day'])) {
            $updatetime = time() - intval($data['day']) * 86400;
            $where['updatetime'] = array("GT", $updatetime);
        }

        $hits = array();
        $result = $this->hits_db->where($where)->order($order)->limit($num)->select();
        foreach ($result as $r) {
            $pos = explode("-", $r['hitsid']);
            //取得点击数所属信息ID
            $ids_array[] = $id = $pos[2];
            $hits[$id] = $r;
        }

        //查询文章条件
        $where = array();
        $ids = implode(',', $ids_array);
        if ($ids) {
            $where['id'] = array("IN", $ids);
        }
        $array = array();
        $result = $this->db->where($where)->getField("id,title,thumb,url,status,username,updatetime",true);
        foreach ($ids_array as $id) {
            if ($result[$id]['title'] != '') {
                $array[$id] = array_merge($result[$id], $hits[$id]);
            }
        }
        //结果进行缓存
        if ($cache) {
            S($cacheID, $array, $cache);
        }
        
        //log
        if (APP_DEBUG && C('LOG_RECORD')) {
            $msg = "ContentTagLib标签->hits：参数：catid=$catid ,modelid=$modelid ,order=$order ,
            SQL:".$this->db->getLastSql()." | ".$this->hits_db->getLastSql();
            Log::write($msg);
        }
        return $array;
    }

    /**
     * 相关文章标签
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 null	 调用栏目ID
     * nid	 否	 null	 排除id 一般是 $id，排除当前文章
     * relation	 否	 $relation	 无需更改
     * keywords	 否	 null	 内容页面取值：$rs[keywords]
     * num	 是	 null	 数据调用数量
     * @param $data
     */
    public function relation($data) {
        $catid = intval($data['catid']);
        if (!$this->set_modelid($catid))
            return false;
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = md5(implode(",", $data['keywords']) . $data['relation']);
        if ($cache && $key_array = S($cacheID)) {
            return $key_array;
        }
        $data['num'] = (int)$data['num'];
        if(!$data['num']){
            $data['num'] = 10;
        }
        $where = array();
        $where['status'] = array("EQ", 99);
        $order = $data['order'];
        $limit = $data['nid'] ? $data['num'] + 1 : $data['num'];

        //根据手动添加的相关文章
        if ($data['relation']) {
            $relations = explode('|', $data['relation']);
            $relations = array_diff($relations, array(null));
            $relations = implode(',', $relations);
            $where['id'] = array("IN", $relations);
            $key_array = $this->db->cache(true)->where($where)->limit($limit)->order($order)->select();
            $getLastSql .= $this->db->getLastSql()."|";
        } elseif ($data['keywords']) {//根据关键字的相关文章
            $keywords_arr = $data['keywords'];
            $key_array = array();
            $number = 0;
            $i = 1;
            foreach ($keywords_arr as $_k) {
                $_k = str_replace('%', '', $_k);
                $where['keywords'] = array("LIKE", '%' . $_k . '%');
                $r = $this->db->cache(true)->where($where)->limit($limit)->order($order)->select();
                $getLastSql .= $this->db->getLastSql()."|";
                $number += count($r);
                foreach ($r as $id => $v) {
                    if ($i <= $data['num'] && !in_array($id, $key_array))
                        $key_array[$v['id']] = $v;
                    $i++;
                }
                if ($data['num'] < $number)
                    break;
            }
        }
        //去除排除信息
        if ($data['nid'])
            unset($key_array[$data['nid']]);

        //结果进行缓存
        if ($cache) {
            S($cacheID, $key_array, $cache);
        }
        
        //log
        if (APP_DEBUG && C('LOG_RECORD')) {
            $msg = "ContentTagLib标签->relation：参数：catid=$catid ,order=$order ,
            SQL:".$getLastSql;
            Log::write($msg);
        }
        
        return $key_array;
    }

    /**
     * 栏目列表（category）
     * 参数名	 是否必须	 默认值	 说明
     * catid	 否	 0	 调用该栏目下的所有栏目 ，默认0，调用一级栏目
     * order	 否	 null	 排序方式、一般按照listorder ASC排序，即栏目的添加顺序
     * @param $data
     */
    public function category($data) {
        //缓存时间
        $cache = (int) $data['cache'];
        $cacheID = md5(implode(",", $data));
        if ($cache && $array = S($cacheID)) {
            return $array;
        }
        $data['catid'] = intval($data['catid']);
        $where = $array = array();
        $db = M("Category");
        $categorys = $this->category;
        $num = (int)$data['num'];
        if ($data['catid'] > 0) {
            $where['parentid'] = array("EQ", $data['catid']);
            if($num){
                $categorys = $db->where($where)->limit($num)->order($data['order'])->select();
            }else{
                $categorys = $db->where($where)->order($data['order'])->select();
            }
        }
        foreach ($categorys as $catid => $cat) {
            if (!$cat['ismenu']) {
                continue;
            }
            if (strpos($cat['url'], '://') === false) {
                $cat['url'] = substr(CONFIG_SITEURL, 0, -1) . $cat['url'];
            }
            if ($cat['parentid'] == $data['catid']) {
                $array[$catid] = $cat;
            }
        }

        //结果进行缓存
        if ($cache) {
            S($cacheID, $array, $cache);
        }
        return $array;
    }

}

?>
