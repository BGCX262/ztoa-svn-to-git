<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-5-21
 * Time: 下午9:10
 * To change this template use File | Settings | File Templates.
 */
class DwtfpersonAction extends AdminbaseAction
{
    function _initialize() {
        parent::_initialize();
        $this->Cusperson = D("Dwtfperson");
        $this->Wtf = D("Dwtfmsg");

        $menuid = (int)$this->_get("menuid");
        $condition['role_id'] = self::$Cache['User']['role_id'];
        $condition['mid'] = $menuid;

        $menurole = D("Access")->where($condition)->find();
        $this->assign('menurole',$menurole);
    }

    public function personadd(){

        $id = (int) $this->_get("id");
        $pid = (int) $this->_get("pid");
        $t = $this->_get("t");
//        $parentid = (int) $this->_get("parentid");
        $aname = ($t=='a') ? '增加' : '修改';

        //$param1 = gp($this->_get('param')); //htmlspecialchars
        //$param1 = json_encode(ser($this->_post('param'),1)); //htmlspecialchars

        $pinfo = $this->Cusperson->where(array('per_id'=>$pid))->find();

        if($t == 'a'){
            $_defsex = '女';
            $_defsts = 'Y';
            $param1 = gp($this->_get('param')); //htmlspecialchars
        }else{
            $_defsex = $pinfo['sex'];
            $_defsts = $pinfo['sts'];
            $param1 = json_encode(ser($this->_post('param'),1)); //htmlspecialchars
        }

        //echo $param1;

        $sel = new stdClass();
        $sel->sex = showMenuParent0('sex',$_defsex,'class="length_2"','',1,'1',1);
        $sel->sts = nysts('停用','使用','sts',$_defsts,'','class="length_2"');
        $this->assign('sel',$sel);

        $info = $this->Cusperson->where(array('wtf_id'=>$id))->order(array("sts" => "DESC"))->select();

        $this->assign("parentid",$id);
        $this->assign("info",$info);
        $this->assign("pinfo",$pinfo);
        $this->assign("t",$t);
        $this->assign("aname", $aname);
        $this->assign('param',$param1);
//        $this->display("Dcustomermsg:personadd");
        $this->display();
    }

    public function personsave(){
        if(IS_POST){
            if($this->_post("per_id") == ''){
                if ($this->Cusperson->create()) {
                    $biry = ($this->_post("biryear")=='') ? '0000' : $this->_post("biryear");
                    $this->Cusperson->birthday = $biry.'-'.$this->_post("birday");
                    if ($this->Cusperson->add()) {
                        $this->success("新增成功！", U("Dwtfperson/personadd",'param='.json_encode(ser($this->_post('param'),1)).'&menuid='.$this->_post('menuid').'&id='.$this->_post('id').'&t='.$this->_post('t')));
                    } else {
                        $this->error("新增失败！");
                    }
                } else {
                    $this->error($this->Cusperson->getError());
                }
            }else{
                if ($this->Cusperson->create()) {
                    $biry = ($this->_post("biryear")=='') ? '0000' : $this->_post("biryear");
                    $this->Cusperson->birthday = $biry.'-'.$this->_post("birday");
                    if ($this->Cusperson->save() !== false) {
                        $this->success("更新成功！", U("Dwtfperson/personadd",'param='.json_encode(ser($this->_post('param'),1)).'&menuid='.$this->_post('menuid').'&id='.$this->_post('id').'&t='.$this->_post('t')));
                    } else {
                        $this->error("更新失败！");
                    }
                } else {
                    $this->error($this->Cusperson->getError());
                }

            }

        }
    }

    public function getcusper(){
        $pid = (int) $this->_post("per_id");
        $info = $this->Cusperson->where(array('per_id'=>$pid))->find();
        $info['biryear'] = substr($info['birthday'],0,4);
        $info['birday'] = substr($info['birthday'],5,5);

        //dump($info);

        echo json_encode($info);

    }

    /**
     *
     * 供应商人员列表
     */
    public function personlist(){

        $_defsex = '女';
        $_defsts = 'Y';
        $sel = new stdClass();
        $sel->sex = showMenuParent0('sex',$_defsex,'class="w80"','',1,'1',1);
        $sel->sts = nysts('停用','使用','sts',$_defsts,'','class="w80"');
        $sel->wtf = R('Dwtfmsg/wtfStr',array(2,'','','id','class="w80"',''));
        $this->assign('sel',$sel);

        C('TOKEN_ON',false);
        $this->display();
    }


   public  function getpersonlist(){
        $opts = gp($this->_post('param'));
        $obj = json_decode($opts);

        $_p = ($obj->p) ? $obj->p : 1;

        if(!empty($obj))
        {
            if(!empty($obj->q_wtf_name)){

                $condsub['wtf_name'] = array('like',"%".$obj->q_wtf_name."%");

                $subQuery = $this->Wtf->field('wtf_id')->where($condsub)->select();
                $wtfid = array2tostr($subQuery);
                $condition['wtf_id'] = array('IN', $wtfid);
            }

            if(!empty($obj->q_tell))  $condition['tell'] =$obj->q_tell;
            if(!empty($obj->q_phone))  $condition['phone'] = $obj->q_phone;
            if(!empty($obj->q_name))  $condition['name'] = array('like',"%".$obj->q_name."%");
            $perpage = (int) $obj->perpage;
        }

        $condition['sts'] = 'Y';

        $pagesize = (isset($perpage) && !empty($perpage))  ? $perpage : ((AppframeAction::$Cache['User']['pagesize']) ? AppframeAction::$Cache['User']['pagesize'] : C("PAGE_LISTROWS") );

        $count = $this->Cusperson->where($condition)->count();

        $totalp = ceil($count/$pagesize);
        $p =  $this->page2($count,$pagesize,$_p);
        $custom = $this->Cusperson->relation(true)->where($condition)->limit($p->firstRow.','.$p->listRows)->order(array("wtf_id" => "ASC"))->select();
        //echo $this->Cusperson->relation(true)->getLastSql();

       /*if(is_array($custom)){
           foreach($custom as $ona)
           {
               $wtfinfo = $this->Wtf->field("wtf_name")->where("wtf_id=$ona[wtf_id]")->find();

               $ona['wtfinfo'] = $wtfinfo;
               $menuall[] = $ona;
           }
       }else{
           $menuall = array();
       }*/


        $page = $p->showAjax();

        if($custom && count($custom)>0){
            $json['list'] = $custom;
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

    /**
     * 供应商联系人信息
     * @param $wtf_id
     */
    public function  getwtfpersoninfo($wtf_id){
         $info = $this->Cusperson->where(array('per_id'=>$wtf_id))->find();
        $wtfinfo = $this->Wtf->field('wtf_name')->where(array('wtf_id'=>$info['wtf_id']))->find();
        $info['wtf_name'] = $wtfinfo['wtf_name'];

        echo json_encode($info);
}

    /**
     * 选择供应商时增加保存
     */
    public function personsaveone(){

        if(IS_POST){
            if($this->_post("per_id") == ''){
                $filedarr = array('name','sex','dept','wtf_group','duty','tell','fax','phone','qq','sts');

                $data =array();
                foreach($filedarr as $onf)
                {
                    $data[$onf] =  $this->_post($onf);
                }


                $data['wtf_id'] = $this->_post("id");

                $biry = ($this->_post("biryear")=='') ? '0000' : $this->_post("biryear");
                $data['birthday'] = $biry.'-'.$this->_post("birday");

                if ($this->Cusperson->add($data)) {
                    //$this->success("新增成功！");
                    echo '1';
                } else {
                    //$this->error("新增失败！");
                    echo '0';
                }


               /* if ($this->Cusperson->create()) {
                    $biry = ($this->_post("biryear")=='') ? '0000' : $this->_post("biryear");
                    $this->Cusperson->birthday = $biry.'-'.$this->_post("birday");
                    if ($this->Cusperson->add()) {
                        //$this->success("新增成功！");
                        echo '1';
                    } else {
                        //$this->error("新增失败！");
                        echo '0';
                    }
                } else {
                    $this->error($this->Cusperson->getError());
                    //echo '2';
                }*/
            }

        }
    }

}
