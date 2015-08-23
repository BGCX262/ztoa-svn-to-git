<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-4-11
 * Time: 下午2:50
 * To change this template use File | Settings | File Templates.
 */
class DwtfmsgAction extends AdminbaseAction{

    function _initialize() {
        parent::_initialize();
        $this->Custom = D("Dwtfmsg");
        $this->Cusperson = D("Dwtfperson");
        $this->Wtfzhmsg = D("Dwtfzhmsg");

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
        $custom = $this->Custom->where($condition)->limit($p->firstRow.','.$p->listRows)->order(array("wsort" => "ASC"))->select();
        //echo $this->Custom->getLastSql();

        if(is_array($custom)){
            foreach($custom as $ona)
            {
                $subcount =$this->Cusperson->where("wtf_id=$ona[wtf_id]")->count();
                $zhcount = $this->Wtfzhmsg->where("wtf_id=$ona[wtf_id]")->count();

                $ona[subcount] = $subcount;
                $ona[zhcount] = $zhcount;
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
            if($this->_post("id") == ''){
                if ($this->Custom->create()) {
                    $this->Custom->opr_no = AppframeAction::$Cache['User']['username'];
                    $this->Custom->opr_time = date('Y-m-d H:i:s');
                    if ($this->Custom->add()) {
                        $this->success("新增成功！", U("Dwtfmsg/index",'param='.gp($this->_post('param')).'&menuid='.$this->_post('menuid')));
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
                        $this->success("更新成功！", U("Dwtfmsg/index",'param='.gp($this->_post('param')).'&menuid='.$this->_post('menuid')));
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

                $sel->type_code = showMenuParent0('type_code','45','class="w90"','',1,'',44);
                $sel->jsstate = showMenuParent0('jsstate','34','class="w90"','',1,'',3);
                $sel->dis_sts = nysts('备用','常用','dis_sts','Y','','class="w90"');

            }else{
                $info = $this->Custom->where(array('wtf_id'=>$id))->find();
                //echo $this->Custom->getLastSql();
                //$parentid = $info.parentid;
                $sel->type_code = showMenuParent0('type_code',$info['type_code'],'class="w90"','',1,'',44);
                $sel->jsstate = showMenuParent0('jsstate',$info['jsstate'],'class="w90"','',1,'',3);
                $sel->dis_sts = nysts('备用','常用','dis_sts',$info['dis_sts'],'','class="w90"');

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

  public  function  getWtfInfo($wtf_id , $datatype=''){
      $info = $this->Custom->where(array('wtf_id'=>$wtf_id))->find();

       return  ($datatype == '') ? $info : json_encode($info);
   }


    /*
     *委托方选择
     */
   public  function wtfStr($fs,$lowtf='',$wfv='',$finame='',$widu='',$sk=''){

        if(!empty($sk))
        {
            //$querypa="select menu_code,menu_name from smenucode where parent_code='4' and menu_code not in('38','39','55','61')  order by sort";
            $par = D("Customsetting")->where(array('statue'=>1))->select();
        }else{
            //$querypa="select menu_code,menu_name from smenucode where parent_code='4' and menu_code not in('38','39','55')  order by sort";
            $par = D("Customsetting")->where(array('statue'=>1))->select();
        }
        //$par=$db->get_sql_arr($querypa);

        if($fs == 1)  {
            $lxsStr='';
        }else{
            $lxsStr="<select name='".$finame."' id='".$finame."'  $widu>";
        }


        if(!empty($finame)){
            $zhi=$wfv == '' ? $_REQUEST[$finame] : $wfv;
        }else{
            $zhi=$wfv;
        }

        if($fs == 3){
            $lxsStr.= "<option value=''>请选择</option>";
            if(is_array($par)){
                foreach($par as $temp){
                    //dump($temp);
                    $subarr=R('Dwtfmsg/GetWtfLog',array($temp['id'],$lowtf));
                    if(is_array($subarr)){
                        $lxsStr .= "<optgroup  label='" . $temp['name'] . "'>";

                        foreach($subarr as $stemp){
                            //dump($stemp);
                            $self = $zhi == $stemp['wtf_name'] ? 'selected' : '';
                            $lxsStr .= "<option value='" . $stemp['wtf_name'] . "' $self>" . $stemp['wtf_name'] . " </option>";
                        }
                    }
                }
            }

        }else{
            $lxsStr.= "<option value=''>请选择</option>";
            if(is_array($par)){
                foreach($par as $temp){
                    $subarr=R('Dwtfmsg/GetWtfLog',array($temp['id'],$lowtf));
                    if(is_array($subarr)){
                        $lxsStr .= "<optgroup  label='" . $temp['name'] . "'>";

                        foreach($subarr as $stemp){
                            $self = $zhi == $stemp['wtf_id'] ? 'selected' : '';
                            $lxsStr .= "<option value='" . $stemp['wtf_id'] . "' $self>" . $stemp['wtf_name'] . " </option>";
                        }
                    }
                }
            }
        }

        if($fs==2 || $fs==3){
            $lxsStr.="</select>";
        }

        return $lxsStr;

    }


    /*
     * 取得某个委托方下所有的数据，并返回数组
     */
    function GetWtfLog($type_code,$wtf_id=''){

        $condition['dis_sts'] = 'Y';

        if(!empty($type_code))   $condition['type_code'] = $type_code;
        if(!empty($wtf_id))   $condition['wtf_id'] = $wtf_id;

        $wtfarr = $this->Custom->field('wtf_id,wtf_name')->where($condition)->order(array("wtf_id" => "ASC"))->select();

        return $wtfarr;
    }



}
