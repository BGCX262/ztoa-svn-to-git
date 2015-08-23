<?php

/* * 
 * 菜单
 * Some rights reserved：85zu.com
 * Contact email:lanbinbin@85zu.com
 */

class MenuModel extends CommonModel {

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '菜单名称不能为空！', 1, 'regex', 3),
        array('app', 'require', '应用不能为空！', 1, 'regex', 3),
        array('model', 'require', '模块名称不能为空！', 1, 'regex', 3),
        array('action', 'require', '方法名称不能为空！', 1, 'regex', 3),
        //array('app,model,action', 'checkAction', '同样的记录已经存在！', 0, 'callback', 1),
        array('parentid', 'checkParentid', '菜单只支持四级！', 1, 'callback', 1),
    );
    //自动完成
    protected $_auto = array(
            //array(填充字段,填充内容,填充条件,附加规则)
    );

    //验证菜单是否超出三级
    public function checkParentid($parentid) {
        $find = $this->where(array("id" => $parentid))->getField("parentid");
        if ($find) {
            $find2 = $this->where(array("id" => $find))->getField("parentid");
            if ($find2) {
                $find3 = $this->where(array("id" => $find2))->getField("parentid");
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }

    //验证action是否重复添加
    public function checkAction($data) {
        //检查是否重复添加
        $find = $this->where($data)->find();
        if ($find) {
            return false;
        }
        return true;
    }

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID  
     * @param integer $with_self  是否包括他自己
     */
    public function admin_menu($parentid, $with_self = false) {
	    //实例化权限表
        $privdb = M("Access");
		$prdb = $privdb->where(array('role_id' => session("roleid")))->select();//获取登录帐号的权限

		if(!empty($prdb)){
		    foreach ($prdb as $t) {
                $p[] = $t['mid'];
            }
		}else{
		    return array();
		}
		

	
        //父节点ID
        $parentid = (int) $parentid;

        $map['parentid'] = $parentid;
        $map['status'] = 1;
        $map['id'] = array('in',$p);
        $result = $this->where($map)->order(array("listorder" => "ASC"))->select();
       // echo $this->getLastSql();

        if ($with_self) {
            $result2[] = $this->where(array('id' => $parentid))->find();
            $result = array_merge($result2, $result);
        }

        return $result;

    }

    /**
     * 获取菜单 头部菜单导航
     * @param $parentid 菜单id
     */
    public function submenu($parentid = '', $big_menu = false) {
        $array = $this->admin_menu($parentid, 1);
        $numbers = count($array);
        if ($numbers == 1 && !$big_menu) {
            return '';
        }
        return $array;
    }

    /**
     * 菜单树状结构集合
     */
    public function menu_json() {
        $Panel = M("AdminPanel")->where(array("userid" => AppframeAction::$Cache['uid']))->select();
        $items['0changyong'] = array(
            "id" => "",
            "name" => "常用菜单",
            "parent" => "changyong",
            "url" => U("Menu/public_changyong"),
        );
        foreach ($Panel as $r) {
            $items[$r['menuid'] . '0changyong'] = array(
                "icon" => "",
                "id" => $r['menuid'] . '0changyong',
                "name" => $r['name'],
                "parent" => "changyong",
                "url" => $r['url'],
            );
        }
        $changyong = array(
            "changyong" => array(
                "icon" => "",
                "id" => "changyong",
                "name" => "常用",
                "parent" => "",
                "url" => "",
                "items" => $items
            )
        );
        $data = $this->get_tree(0);
//        return array_merge($changyong, $data);
        return  $data;
    }

    //取得树形结构的菜单
    public function get_tree($myid, $parent = "", $Level = 1) {
        $data = $this->admin_menu($myid);
        $Level++;
        if (is_array($data)) {
            foreach ($data as $a) {
                $id = $a['id'];
                $name = ucwords($a['app']);
                $model = ucwords($a['model']);
                $action = $a['action'];
                //附带参数
                $fu = "";
                if ($a['data']) {
                    $fu = "?" . $a['data'];
                }
                $array = array(
                    "icon" => "",
                    "id" => $id . $name,
                    "name" => $a['name'],
                    "parent" => $parent,
                    "url" => U("{$name}/{$model}/{$action}{$fu}", array("menuid" => $id)),
                );
                $ret[$id . $name] = $array;
                $child = $this->get_tree($a['id'], $id, $Level);
                //由于后台管理界面只支持三层，超出的不层级的不显示
                if ($child && $Level <= 3) {
                    $ret[$id . $name]['items'] = $child;
                }
            }
        }
        return $ret;
    }

    /**
     * 更新缓存
     * @param type $data
     * @return type
     */
    public function menu_cache($data = null) {
        if (empty($data)) {
            $data = $this->select();
            F("Menu", $data);
        } else {
            F("Menu", $data);
        }
        return $data;
    }

    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Menu", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->_before_write($data);
    }

}

?>