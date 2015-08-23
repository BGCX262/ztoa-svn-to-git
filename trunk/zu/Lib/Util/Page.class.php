<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

class Page extends Think {
    // 起始行数
    public $firstRow	;
    // 列表每页显示行数
    public $listRows	;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage   ;
    // 分页url定制
    protected $urlrule;


    /**
    +----------------------------------------------------------
     * 架构函数
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
    +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows,$p='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = C('PAGE_ROLLPAGE') ? C('PAGE_ROLLPAGE') : 2;
        $this->listRows = !empty($listRows)?$listRows:C('PAGE_LISTROWS');
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        if (!define('PAGESTOTAL')) define('PAGESTOTAL', $this->totalPages);
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        if($p){
            $this->nowPage =$p;
        }else{
            $this->nowPage  = !empty($_GET[C('VAR_PAGE')])?intval($_GET[C('VAR_PAGE')]):1;
        }
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function  show(){

        if($this->totalRows == 0 OR $this->listRows == 0 OR $this->totalPages <= 1){
            return '';
        }

        $urlrule =  str_replace('%7B%24page%7D','{$page}',$this->urlrule); //urldecode
        if(!$urlrule){
            $p = C('VAR_PAGE');
            $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
            $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
            $parse = parse_url($url);
            if(isset($parse['query'])) {
                parse_str($parse['query'],$params);
                unset($params[$p]);
                $urlrule   =  $parse['path'].'?'.http_build_query($params);
            }
            $urlrule = $urlrule."&".$p.'={$page}';
        }


        $pre_page = $this->nowPage-1;
        $next_page = $this->nowPage +1;

        if($this->nowPage >=$this->totalPages){
            $next_page =  $this->nowPage = $this->totalPages;
        }
        if($this->nowPage <= 1){
            $pre_page =  $this->nowPage = 1;
        }

        $output = '';
        $output .= '<a class="a1">'.$this->totalRows.L('page_item').'</a>';
        $output .= '<a href="'.$this->pageurl($urlrule, 1,$this->parameter).'">'.L('first_page').'</a>';
        $output .= '<a href="'.$this->pageurl($urlrule, $pre_page,$this->parameter).'">'.L('previous').'</a>';
        $show_nums = $this->rollPage*2+1;// 显示页码的个数

        if($this->totalPages <= $show_nums){
            for($i = 1;$i<=$this->totalPages;$i++){
                if($i == $this->nowPage){
                    $output .= '<span>'.$i.'</span>';
                }else{
                    $output .= '<a href="'.$this->pageurl($urlrule,$i,$this->parameter).'">'.$i.'</a>';
                }
            }
        }else{
            if($this->nowPage < (1+$this->rollPage)){
                for($i = 1;$i<=$show_nums;$i++){
                    if($i == $this->nowPage){
                        $output .=  '<span>'.$i.'</span>';
                    }else{
                        $output .= '<a href="'.$this->pageurl($urlrule,$i,$this->parameter).'">'.$i.'</a>';
                    }
                }
            }else if($this->nowPage >= ($this->totalPages - $this->rollPage)){
                for($i = $this->totalPages - $show_nums ; $i <= $this->totalPages ; $i++){
                    if($i == $this->nowPage){
                        $output .=  '<span>'.$i.'</span>';
                    }else{
                        $output .= '<a href="'.$this->pageurl($urlrule,$i,$this->parameter).'">'.$i.'</a>';
                    }
                }
            }else{
                $start_page = $this->nowPage - $this->rollPage;
                $end_page = $this->nowPage + $this->rollPage;
                for($i = $start_page ; $i<=$end_page ; $i++){
                    if($i == $this->nowPage){
                        $output .=  '<span>'.$i.'</span>';
                    }else{
                        $output .= '<a href="'.$this->pageurl($urlrule,$i,$this->parameter).'">'.$i.'</a>';
                    }
                }
            }
        }

        $output .='<a href="'.$this->pageurl($urlrule,$next_page,$this->parameter).'">'.L('next')."</a>";
        $output .='<a href="'.$this->pageurl($urlrule,$this->totalPages,$this->parameter).'">'.L('Last_page')."</a>";
        return $output;
    }


    /**
    +----------------------------------------------------------
     * 分页显示
     * 用于在页面显示的分页栏的输出
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     */
    public function showAjax($jsFun='ajajPage'){
        //当前页数
        $nowPage = $this->nowPage;
        //总页数
        $totalPages = $this->totalPages;
        //总行数
        $totalRows = $this->totalRows;

        $fstr='<a href="javaScript:;" onClick="%fun%" >首&nbsp;&nbsp;页</a>  ';
        $pstr='<a href="javaScript:;" onClick="%fun%" >上一页</a>  ';
        $nstr='<a href="javaScript:;" onClick="%fun%" >下一页</a>  ';
        $estr='<a href="javaScript:;" onClick="%fun%" >尾&nbsp;&nbsp;页</a>  ';
        $goto='<a href="javaScript:;" onClick="%fun%" >%num%</a>  ';
        $now='<a href="javaScript:;" >'.$nowPage.'</a>  ';
        $showstr = '共'.$totalRows.'条记录&nbsp;&nbsp;&nbsp;&nbsp;每页显示 '.$this->listRows.' 条&nbsp;&nbsp;&nbsp;&nbsp;当前第'.$nowPage.'页/共'.$totalPages.'页  ';

        if ($nowPage>1){
            $fstr=str_replace('%fun%',$jsFun."('1')",$fstr);
            $pstr=str_replace('%fun%',$jsFun."('".($nowPage-1)."')",$pstr);
        }else{
            $fstr=str_replace('%fun%','',$fstr);
            $pstr=str_replace('%fun%','',$pstr);
        }

        if ($nowPage!=$totalPages && $nowPage>0){
            $estr=str_replace('%fun%',$jsFun."('".$totalPages."')",$estr);
            $nstr=str_replace('%fun%',$jsFun."('".($nowPage+1)."')",$nstr);
        }else{
            $estr=str_replace('%fun%','',$estr);
            $nstr=str_replace('%fun%','',$nstr);
        }
        $numstr = '';
        /*for ($i=1;$i<=$totalPages;$i++){
                if($i!=$nowPage){
                    $numtmp = str_replace('%num%',$i,$goto);
                    $numtmp = str_replace('%fun%',$jsFun."('".$i."')",$numtmp);
                }else{
                    $numtmp=$now;
                }
                $numstr .= $numtmp;
            }*/

        $numstr.="到第<select name='topage' style='width:70px' onchange=javascript:$jsFun(this.value);>\n";

        for($i=1;$i<=$totalPages;$i++)
        {
            if($i==$nowPage)
            {
                $numstr.="<option value='$i' selected>$i</option>\n";
            }else{
                $numstr.="<option value='$i'>$i</option>\n";
            }
        }

        $numstr.="</select>页";

        $pagestr=$showstr.$fstr.$pstr.$nstr.$estr.$numstr;
        return $pagestr;
    }

    public function pageurl($urlrule, $page, $array = array())
    {
        @extract($array, EXTR_SKIP);
        if(is_array($urlrule))
        {
            //$urlrules = explode('|', $urlrule);
            $urlrule = $page < 2 ? $urlrule[0] : $urlrule[1];
        }
        $url = str_replace('{$page}', $page, $urlrule);
        return $url;
    }

}
?>