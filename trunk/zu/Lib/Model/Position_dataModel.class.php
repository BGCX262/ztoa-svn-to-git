<?php

class Position_dataModel extends CommonModel {

    //自动验证
    protected $_validate = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    );

    /**
     * 推荐位中被推送过来的信息编辑
     * @param type $data
     * @return boolean
     */
    public function Position_edit($data) {
        if (!is_array($data)) {
            return false;
        }
        if(!$data['posid'] || !$data['modelid'] || !$data['id']){
            return false;
        }
        //载入数据处理类
        if (require_cache(RUNTIME_PATH . 'content_input.class.php') == false) {
            return false;
        }

        $content_input = new content_input($data['modelid']);
        //数据处理
        foreach ($data['data'] as $field => $value) {
            //字段类型
            $func = $content_input->fields[$field]['formtype'];
            if (method_exists($content_input, $func)){
                $data['data'][$field] = $content_input->$func($field, $value);
            }else{
                $data['data'][$field] = $value;
            }
        }
        $data['data'] = serialize($data['data']);
        if($this->save($data) !== false){
            service("Attachment")->api_update('', 'position-' . $data['modelid'] . '-' . $data['id'], 1);
            return true;
        }
        return false;
    }

    /**
     * 信息从推荐位中移除
     * @param type $posid
     * @param type $id
     * @param type $modelid]
     */
    public function delete_item($posid, $id, $modelid) {
        if (!$posid || !$id || !$modelid) {
            return false;
        }
        $sql['id'] = $id;
        $sql['modelid'] = $modelid;
        $sql['posid'] = intval($posid);
        $this->where($sql)->delete();
        $this->content_pos($id, $modelid);
        //删除相关联的附件
        service("Attachment")->api_delete('position-' . $modelid . '-' . $id);
    }

    /**
     * 更新信息推荐位状态
     * @param type $id 信息id
     * @param type $modelid 模型id
     * @return type
     */
    public function content_pos($id, $modelid) {
        $id = intval($id);
        $modelid = intval($modelid);
        $MODEL = F("Model");
        $db = M(ucwords($MODEL[$modelid]['tablename']));
        $posids = $this->where(array('id' => $id, 'modelid' => $modelid))->find() ? 1 : 0;
        return $db->where(array('id' => $id))->data(array('posids' => $posids))->save();
    }

}

?>
