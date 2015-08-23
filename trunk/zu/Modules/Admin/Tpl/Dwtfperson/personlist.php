<?php if (!defined('ZU_VERSION')) exit();
?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<style>
    .pop_nav{
        padding: 0px;
    }
    .pop_nav ul{
        border-bottom:1px solid #266AAE;
        padding:0 5px;
        height:25px;
        clear:both;
    }
    .pop_nav ul li.current a{
        border:1px solid #266AAE;
        border-bottom:0 none;
        color:#333;
        font-weight:700;
        background:#F3F3F3;
        position:relative;
        border-radius:2px;
        margin-bottom:-1px;
    }

</style>
<div class="wrap J_check_wrap">
    <div class="pop_nav">
        <ul class="J_tabs_nav">
            <li class="current" id="cus_list"><a href="javascript:;">供应商联系人</a></li>
            <li class="" id="cus_form"><a href="javascript:;">供应商联系人增加</a></li>
        </ul>
    </div>

        <div class="J_tabs_contents">

            <div  style="display:;" id="cus_div_list">
                <div class="h_a">供应商联系人</div>

                <div class="search_type cc mb10">
                    <div class="mb10"> <span class="mr20">
    供应商：<input type="text" class="input length_2" name="wtf_name" id="wtf_name" size='20' value="{$obj:q_wtf_name}">
      姓名：<input type="text" class="input length_2" name="qname" id="qname" size='20' value="{$obj:q_name}">
       电话：<input type="text" class="input length_2" name="qtell" id="qtell" size='20' value="{$obj:q_tell}">
       手机：<input type="text" class="input length_2" name="qphone" id="qphone" size='20' value="{$obj:q_phone}">
       每页显示：<input type="text" class="input length_2" name="perpage" id="perpage" size='20' value="{$obj:perpage}">
      <button class="btn"  onclick="seachFun();">搜索</button>

      </span> </div>
                </div>

                <div class="p10">
                    <div class="pages" id="page_div"> {$Page}</div>
                </div>

                    <div class="table_list">
                        <table width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <td width="w10">供应商</td>
                                <td class="w10 ">姓名</td>
                                <td class="w5">性别</td>
                                <td  class="w10 ">部门</td>
                                <td class="w10">组别</td>
                                <td class="w5 ">职务</td>
                                <td class="w10">电话</td>
                                <td  class="w10 ">传真</td>
                                <td class="w10">手机</td>
                                <td  class="w10 ">QQ</td>
                                <td width="w5">操作</td>
                            </tr>
                            </thead>
                            <tbody id="list-data">
                            </tbody>
                        </table>
                        <div class="p10">
                            <div class="pages" id="page_div1"> {$Page} </div>
                        </div>
                    </div>
                    <input type="hidden" name="p" id="p" value='{$obj:p}'>
            </div>

            <div  style="display:none;" id="cus_div_form">
                <!--<form class="J_ajaxForm" name="myform" id="myform" action="{:U('Dwtfperson/personsaveone')}" method="post">-->
                <div class="h_a">供应商联系人增加</div>
                <input type="hidden" name="per_id" id="per_id" value="">
                <input type="hidden" id="dataurl" value="{:U('Dwtfperson/getwtfpersoninfo')}">
                <div class="table_full">
                    <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                        <tbody>
                        <tr>
                            <th class="w10  aright">姓名{:C("separator");}</th>
                            <td class="w20"><input type="text" class="input  w80" name="name" id="name"  value="{$pinfo.name}"><label class="red">*</label></td>
                            <th class="w10  aright">性别{:C('separator');}</th>
                            <td class="w20">{$sel:sex}</td>
                            <th class="w10 aright">生日{:C("separator");}</th>
                            <td class="w30"><input type="text" class="input  w30" name="biryear" id="biryear"  value="{$pinfo.birthday|substr=0,4}" onclick="WdatePicker({dateFmt:'yyyy'});" readonly>
                                <input type="text" class="input  w40" name="birday" id="birday"  value="{$pinfo.birthday|substr=5,5}" onclick="WdatePicker({dateFmt:'MM-dd'});" readonly></td>
                        </tr>
                        <tr>
                            <th class="w10  aright">部门{:C("separator");}</th>
                            <td class="w20"><input type="text" class="input  w80" name="dept" id="dept"  value="{$pinfo.dept}"></td>
                            <th class="w10  aright">组别{:C('separator');}</th>
                            <td class="w20"><input type="text" class="input  w80" name="wtf_group" id="wtf_group"  value="{$pinfo.wtf_group}"></td>
                            <th class="w10  aright">职务{:C("separator");}</th>
                            <td class="w30"><input type="text" class="input  w80" name="duty" id="duty"  value="{$pinfo.duty}"></td>
                        </tr>
                        <tr>
                            <th class="w10  aright">电话{:C('separator');}</th>
                            <td class="w20"><input type="text" class="input  w80" name="tell" id="tell"  value="{$pinfo.tell}"></td>
                            <th  class="w10  aright">传真{:C("separator");}</th>
                            <td class="w20"><input type="text" class="input  w80" name="fax" id="fax"  value="{$pinfo.fax}"></td>
                            <th  class="w10  aright">手机{:C('separator');}</th>
                            <td class="w30"><input type="text" class="input  w80" name="phone" id="phone"  value="{$pinfo.phone}"></td>
                        </tr>
                        <tr>
                            <th  class="w10 aright">QQ{:C('separator');}</th>
                            <td class="w20"><input type="text" class="input  w80" name="qq" id="qq"  value="{$pinfo.qq}"></td>
                            <th class="w10 aright">状态{:C("separator");}</th>
                            <td class="w20">{$sel:sts}</td>
                            <th  class="w10 aright">供应商{:C('separator');}</th>
                            <td class="w30">{$sel:wtf}<label class="red">*</label></td>
                        </tr>
                        <tr>
                            <td colspan="6">
                               <button class="btn btn_submit mr10 J_ajax_submit_btn" onclick="personsave();" type="submit">确定增加</button><span id="errmsg"> </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <!--</form>-->
            </div>

</div>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>
<script type="text/javascript">
    $(document).ready(function(){
        ajajPage($('#p').val());
    });
    function ajajPage(p){
        if(isNaN(p) || p =='') p=1;

        var param={};
        param.q_wtf_name =  $('#wtf_name').val();
        param.q_tell =  $('#qtell').val();
        param.q_phone =  $('#qphone').val();
        param.q_name =  $('#qname').val();
        param.perpage = $('#perpage').val();
        param.status = $('#status').val();
        param.p = p;

        $.ajax({
            type: "POST",
            url: "{:U('Dwtfperson/getpersonlist')}",
            data: "&zu=85&p="+p+"&param="+encodeURIComponent(JSON2.stringify(param)),
            dataType: "json",
            beforeSend: function(){
                comload('list-data',11,'{$config_siteurl}',"{:C('LODINGTEXT')}");
            },
            success: function(json){

                if(json.error=='' || json.error==null || json.error==undefined){
                    $('#list-data').html('');

                    if(json.list.length == 0)
                    {
                        nodata('list-data',11,"{:C('NODATA')}");
                    }

                    $(json.list).each(function(){
                        //console.log(this);

                        trchangcolor('list-data');

                        td = tr.insertCell(-1);//
                        var wtfinfo = this.wtfinfo;
                        td.appendChild(document.createTextNode(wtfinfo.wtf_name));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.name));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.sex));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.dept));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.wtf_group));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.duty));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.tell));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.fax));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.phone));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.qq));


                        td = tr.insertCell(-1);//
                        var unique = Misc.unique();
                        var radio = document.createElement("input");
                            radio.type='radio';
                            radio.name='wtfperid';
                            radio.id = unique;
                            radio.value = this.per_id;

                        var label = document.createElement("label");
                            label.setAttribute("for",unique);
                            label.appendChild(document.createTextNode("选择"))

                        td.appendChild(radio);
                        td.appendChild(label);

                    });

                    setpage(json);

                }else{  //
                    alert('错误提示：'+json.error);
                }
            }
        });
    }

    function seachFun(){
        ajajPage($('#p').val());
    }

    function enterPress(e)
    {
        if (e.keyCode == 13)
        {
            ajajPage($('#p').val());
        }
    }

    function personsave(){

      var name =  $('#name').val();
      var sex =  $('#sex').val();
      var biryear =  $('#biryear').val();
      var birday =  $('#birday').val();
      var dept =  $('#dept').val();
      var wtf_group =  $('#wtf_group').val();
      var duty =  $('#duty').val();
      var tell =  $('#tell').val();
      var fax =  $('#fax').val();
      var phone =  $('#phone').val();
      var qq =  $('#qq').val();
      var sts =  $('#sts').val();
      var id =  $('#id').val();

        if(name == ''){
            Wind.use('artDialog','iframeTools',function(){
                art.dialog.alert("姓名不能为空！");
            });
            return false;
        }

        if(id == ''){
            Wind.use('artDialog','iframeTools',function(){
                art.dialog.alert("供应商不能为空！");
            });
            return false;
        }

        $.ajax({
            type:"POST",
            url:"{:U('Dwtfperson/personsaveone')}",
            data:{
                name:name,
                sex:sex,
                biryear:biryear,
                birday:birday,
                dept:dept,
                wtf_group:wtf_group,
                duty:duty,
                tell:tell,
                fax:fax,
                phone:phone,
                qq:qq,
                sts:sts,
                id:id
             },
            dataType: "json",
            success:function(json){
                if(json == 1){
                    $("#cus_list").attr("class",'current');
                    $("#cus_form").attr("class",'');

                    $("#cus_div_list").css("display","");
                    $("#cus_div_form").css("display","none");

                    $('input[class^="input w80"]').val('');
                    $('#sts').val("Y");
                    $('#sex').val("女");
                    $('#id').val('');
                    ajajPage($('#p').val());
                }else{
                    $("#errmsg").html("新增失败！！！");
                }
            }
        });
    }
</script>