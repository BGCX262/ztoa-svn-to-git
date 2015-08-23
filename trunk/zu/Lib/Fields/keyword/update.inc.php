<?php

/**
 * 关键字整理，增加到TAG表
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */
function keyword($field, $value) {
    if (!empty($value)) {
        $db = M("Tags");
        $time = time();
        if (strpos($value, ',') === false) {
            $keyword = explode(' ', $value);
        } else {
            $keyword = explode(',', $value);
        }
        $keyword = array_unique($keyword);
        $data = array();
        //新增
        if (ACTION_NAME == 'add') {
            foreach ($keyword as $v) {
                if (empty($v) || $v == '') {
                    continue;
                }
                if ($db->where(array("tag" => $v))->find()) {
                    $db->where(array("tag" => $v))->setInc('usetimes');
                } else {
                    $db->data(array(
                        "tag" => $v,
                        "usetimes" => 1,
                        "lastusetime" => $time,
                        "lasthittime" => $time,
                    ))->add();
                }
                $data[] = array(
                    'tag' => $v,
                    "url" => $this->data['url'],
                    "title" => $this->data['title'],
                    "modelid" => $this->modelid,
                    "contentid" => $this->id,
                    "catid" => $this->data['catid'],
                    "updatetime" => $time,
                );
            }
            M("Tags_content")->addAll($data);
        } else {
            $tags = M("Tags_content")->where(array(
                        "modelid" => $this->modelid,
                        "contentid" => $this->id,
                        "catid" => $this->data['catid'],
                    ))->select();
            foreach ($tags as $key => $value) {
                //如果在新的关键字数组找不到，说明已经去除
                if (!in_array($value['tag'], $keyword)) {
                    //删除不存在的tag
                    M("Tags_content")->where(array("tag" => $value['tag'], "modelid" => $value['modelid'], "contentid" => $value['contentid'], "catid" => $value['catid']))->delete();
                    $db->where(array("tag" => $value['tag']))->setDec('usetimes');
                } else {
                    //更新URL
                    M("Tags_content")->where(array("tag" => $value['tag'], "modelid" => $value['modelid'], "contentid" => $value['contentid'], "catid" => $value['catid']))->data(array("url" => $this->data['url']))->save();
                    foreach ($keyword as $k => $v) {
                        if ($value['tag'] == $v) {
                            unset($keyword[$k]);
                        }
                    }
                }
            }
            //新增的tags
            if (count($keyword) > 0) {
                foreach ($keyword as $v) {
                    if (empty($v) || $v == '') {
                        continue;
                    }
                    if ($db->where(array("tag" => $v))->find()) {
                        $db->where(array("tag" => $v))->setInc('usetimes');
                    } else {
                        $db->data(array(
                            "tag" => $v,
                            "usetimes" => 1,
                            "lastusetime" => $time,
                            "lasthittime" => $time,
                        ))->add();
                    }
                    $data[] = array(
                        'tag' => $v,
                        "url" => $this->data['url'],
                        "title" => $this->data['title'],
                        "modelid" => $this->modelid,
                        "contentid" => $this->id,
                        "catid" => $this->data['catid'],
                        "updatetime" => $time,
                    );
                }
                M("Tags_content")->addAll($data);
            }
        }
    }
}

?>
