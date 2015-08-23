<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-5-21
 * Time: 下午9:10
 * To change this template use File | Settings | File Templates.
 */
class DcuspersonAction extends AdminbaseAction
{
    function _initialize() {
        parent::_initialize();
        $this->Cusperson = D("Dcusperson");

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

        $info = $this->Cusperson->where(array('customer_id'=>$id))->order(array("sts" => "DESC"))->select();

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
                        $this->success("新增成功！", U("Dcusperson/personadd",'param='.json_encode(ser($this->_post('param'),1)).'&menuid='.$this->_post('menuid').'&id='.$this->_post('id').'&t='.$this->_post('t')));
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
                        $this->success("更新成功！", U("Dcusperson/personadd",'param='.json_encode(ser($this->_post('param'),1)).'&menuid='.$this->_post('menuid').'&id='.$this->_post('id').'&t='.$this->_post('t')));
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

}
