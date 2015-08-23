<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-4-11
 * Time: 下午2:50
 * To change this template use File | Settings | File Templates.
 */
class DcustomermsgAction extends AdminbaseAction{

    function _initialize() {
        parent::_initialize();
        $this->Custom = D("Dcustomermsg");
        $this->Cusperson = D("Dcusperson");

        $menuid = (int)$this->_get("menuid");
        $condition['role_id'] = self::$Cache['User']['role_id'];
        $condition['mid'] = $menuid;

        $menurole = D("Access")->where($condition)->find();
        $this->assign('menurole',$menurole);
    }

    public function index(){
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
            if(!empty($obj->q_province))  $condition['province'] =$obj->q_province;
            if(!empty($obj->q_city))  $condition['city'] =$obj->q_city;
            if(!empty($obj->q_area))  $condition['area'] = $obj->q_area;
           // if(!empty($obj->status)) $condition['status'] = $obj->status;
            if(!empty($obj->q_address))  $condition['address'] = array('like',"%".$obj->q_address."%");
            $perpage = (int) $obj->perpage;
        }

        //$condition['parentid'] = 0;

        $pagesize = (isset($perpage) && !empty($perpage))  ? $perpage : ((AppframeAction::$Cache['User']['pagesize']) ? AppframeAction::$Cache['User']['pagesize'] : C("PAGE_LISTROWS") );

        $count = $this->Custom->where($condition)->count();

        $totalp = ceil($count/$pagesize);
        $p =  $this->page2($count,$pagesize,$_p);
        $custom = $this->Custom->where($condition)->limit($p->firstRow.','.$p->listRows)->order(array("customer" => "ASC"))->select();
        //echo $this->Custom->getLastSql();

        if(is_array($custom)){
            foreach($custom as $ona)
            {
                $subcount =$this->Cusperson->where("customer_id=$ona[customer_id]")->count();

                $ona[subcount] = $subcount;
                $menuall[] = $ona;
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
            if($this->_post("t") == 'a'){
                if ($this->Custom->create()) {
                      $this->Custom->opr_no = AppframeAction::$Cache['User']['username'];
                      $this->Custom->opr_time = date('Y-m-d H:i:s');
                    if ($this->Custom->add()) {
                        $this->success("新增成功！", U("Dcustomermsg/index",'param='.gp($this->_post('param')).'&menuid='.$this->_post('menuid')));
                    } else {
                        $this->error("新增失败！");
                    }
                } else {
                    $this->error($this->Custom->getError());
                }
            }else{
                if ($this->Custom->create()) {
                    $this->Custom->opr_no = AppframeAction::$Cache['User']['username'];
                    $this->Custom->opr_time = date('Y-m-d H:i:s');
                    if ($this->Custom->save() !== false) {
                        $this->success("更新成功！", U("Dcustomermsg/index",'param='.gp($this->_post('param')).'&menuid='.$this->_post('menuid')));
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

            $sel = new stdClass();



            if(empty($id)){

                $info = array();
                //$info['opr_no'] =  AppframeAction::$Cache['User']['username'];
               // $info['opr_time'] =  date('Y-m-d H:i:s');
                $sel->cus_group = showMenuParent0('cus_group','25','class="length_4"','',1,'',19);
                $sel->hzstate = showMenuParent0('hzstate','31','class="length_4"','',1,'',24);
                $sel->xylevel = showMenuParent0('xylevel','40','class="length_4"','',1,'',39);
                $sel->tzlevel = showMenuParent0('tzlevel','40','class="length_4"','',1,'',39);
                $sel->jsstate = showMenuParent0('jsstate','34','class="length_4"','',1,'',3);
                $sel->cus_type = nysts('传统','线上','cus_type','N','','class="length_4"');

            }else{
                $info = $this->Custom->where(array('customer_id'=>$id))->find();
                //echo $this->Custom->getLastSql();
                //$parentid = $info.parentid;
                $sel->cus_group = showMenuParent0('cus_group',$info['cus_group'],'class="length_4"','',1,'',19);
                $sel->hzstate = showMenuParent0('hzstate',$info['hzstate'],'class="length_4"','',1,'',24);
                $sel->xylevel = showMenuParent0('xylevel',$info['xylevel'],'class="length_4"','',1,'',39);
                $sel->tzlevel = showMenuParent0('tzlevel',$info['tzlevel'],'class="length_4"','',1,'',39);
                $sel->jsstate = showMenuParent0('jsstate',$info['jsstate'],'class="length_4"','',1,'',3);
                $sel->cus_type = nysts('传统','线上','cus_type',$info['cus_type'],'','class="length_4"');

            }
            //$parentid = 0;
//            dump( $info);

            $this->assign('sel',$sel);
            $this->assign("parentid",$parentid);
            $this->assign("info",$info);
            $this->assign("t",$t);
            $this->assign("aname", $aname);
            $this->assign('param',$param1);
            $this->display();
        }
    }




}
