<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-4-11
 * Time: 下午2:50
 * To change this template use File | Settings | File Templates.
 */
class CustomsettingAction extends AdminbaseAction{

    function _initialize() {
        parent::_initialize();
        $this->Custom = D("Customsetting");

        $menuid = (int)$this->_get("menuid");
         $condition['role_id'] = self::$Cache['User']['role_id'];
         $condition['mid'] = $menuid;

         $menurole = D("Access")->where($condition)->find();
        $this->assign('menurole',$menurole);

//         echo  D("Access")->getLastSql();

    }

     public function customlist(){

		 $obj =ser($this->_get('param'),1);
         $p =isset($obj->p )? intval($obj->p) : 1;

         $this->assign('obj',$obj);
         $this->display();
    }

    function getlist(){
        $opts = gp($this->_post('param'));
        $obj = json_decode($opts);

        $_p = ($obj->p) ? $obj->p : 1;

        if(!empty($obj))
        {
            if(!empty($obj->q_name)) $condition['name'] = array('like',"%".$obj->q_name."%");
            if(!empty($obj->status)) $condition['status'] = $obj->status;
            $perpage = (int) $obj->perpage;
        }

        $condition['parentid'] = 0;

        $pagesize = (isset($perpage) && !empty($perpage))  ? $perpage : ((AppframeAction::$Cache['User']['pagesize']) ? AppframeAction::$Cache['User']['pagesize'] : C("PAGE_LISTROWS") );

        $count = $this->Custom->where($condition)->count();
        $totalp = ceil($count/$pagesize);
        $p =  $this->page2($count,$pagesize,$_p);
        $custom = $this->Custom->where($condition)->limit($p->firstRow.','.$p->listRows)->order(array("sort" => "ASC"))->select();


        if(is_array($custom)){
            foreach($custom as $ona)
            {
                $submenu =$this->Custom->field(true)->where("parentid=$ona[id]")->order('sort asc')->select();

                if(empty($submenu))
                {
                    //continue;
                    $ona[sub] = array();
                    $menuall[] = $ona;
                }else{

                    $ona[sub] = $submenu;
                    $menuall[] = $ona;
                }
            }
        }else{
            $menuall = array();
        }


        $page = $p->showAjax();

        if($menuall && count($menuall)>0){
            $json['list'] = $menuall;
            $json['page'] = $page;
            $json['p'] = $_p;
            $json['totalp'] = $totalp;
        }else{
            $json['list'] = array();
            $json['page'] = '';
            $json['p'] = 1;
            $json['totalp'] = 1;
        }

        echo json_encode($json);

    }

    public function  add(){
        if(IS_POST){
            /*if ($this->_post("name") == "") {
                $this->error("请输入名称！");
            }*/

            if($this->_post("t") == 'a'){
                    if ($this->Custom->create()) {
                        if ($this->Custom->add()) {
                            $this->success("新增成功！", U("Customsetting/customlist",'param='.gp($this->_post('param')).'&menuid='.$this->_post('menuid')));
                        } else {
                            $this->error("新增失败！");
                        }
                    } else {
                        $this->error($this->Custom->getError());
                    }
            }else{
                if ($this->Custom->create()) {
                    if ($this->Custom->save() !== false) {
                        $this->success("更新成功！", U("Customsetting/customlist",'param='.gp($this->_post('param')).'&menuid='.$this->_post('menuid')));
                    } else {
                        $this->error("更新失败！");
                    }
                } else {
                    $this->error($this->Custom->getError());
                }

            }

        }else{
            $id = (int) $this->_get("id");
            $t = $this->_get("t");
            $parentid = (int) $this->_get("parentid");
            $aname = ($t=='a') ? '增加' : '修改';

            $param1 = gp($this->_get('param')); //htmlspecialchars
			//$objparam = json_decode($param1);
            //dump($objparam);
			//$param1 = json_encode($objparam);
			//echo $param1;
           // $param1 = stripslashes($param1);

            if(empty($id)){

                $info = array();

            }else{
                $info = $this->Custom->where(array('id'=>$id))->find();
                //$parentid = $info.parentid;

            }
            //$parentid = 0;
//            dump( $info);

            $this->assign("parentid",$parentid);
            $this->assign("info",$info);
            $this->assign("t",$t);
            $this->assign("aname", $aname);
            $this->assign('param',$param1);
            $this->display();
        }
    }


    function IsJsonString($str){
        try{
            $jObject = json_decode($str);
        }catch(Exception $e){
            return false;
        }
        return (is_object($jObject)) ? true : false;
    }

}
