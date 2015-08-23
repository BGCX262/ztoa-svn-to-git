<?php if (!defined('ZU_VERSION')) exit(); ?>
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
        <li class="current" id="cus_list"><a href="javascript:;" onclick="javascript:backcuslist();">{$menuinfo.name}联系人列表</a></li>
        <li class="" id="cus_form"><a href="javascript:;" onclick="javascript:backcusadd();">{$menuinfo.name}联系人{$aname}</a></li>
    </ul>
</div>
<form class="J_ajaxForm" name="myform" id="myform" action="{:U('Dcusperson/personsave')}" method="post">
<div class="J_tabs_contents">

<div  style="display:;" id="cus_div_list">
    <div class="h_a">{$menuinfo.name}联系人列表</div>
    <div class="table_full">
        <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
            <tr>
                <td class="w5 ">姓名</td>
                <td class="w5">性别</td>
                <td  class="w10 ">部门</td>
                <td class="w10">组别</td>
                <td class="w5 ">职务</td>
                <td class="w10">电话</td>
                <td  class="w10 ">传真</td>
                <td class="w10">手机</td>
                <td class="w10 ">生日</td>
                <td  class="w10 ">QQ</td>
                <td class="w5 ">状态</td>
                <td  class="w5 ">信用额度</td>
                <td  class="w5">操作</td>
            </tr>
            <tbody id="data-list">
            <foreach name="info" item="vo">
                <tr>
                    <th class="w5 ">{$vo.name}</th>
                    <td class="w5">{$vo.sex}</td>
                    <th  class="w10 ">{$vo.dept}</th>
                    <td class="w10">{$vo.cus_group}</td>
                    <th class="w5 ">{$vo.duty}</th>
                    <td class="w10">{$vo.tell}</td>
                    <th  class="w10">{$vo.fax}</th>
                    <td class="w10">{$vo.phone}</td>
                    <th class="w10 ">{$vo.birthday}</th>
                    <td class="w10">{$vo.qq}</td>
                    <th  class="w5"><?php echo ($vo['sts']=='Y') ? '使用' : '停用'; ?></th>
                    <td class="w5">{$vo.cus_edu}</td>
                    <td class="w5"> <?php if($menurole['e_m'] == 'Y') {?>
                        <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="修改" onclick="cusmodify(<?php echo $vo['per_id'];?>);">
                        <?php }?>
                    </td>
                </tr>
            </foreach>
            <tr>
                <td colspan="13">
                    <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="增加联系人" onclick="backcusadd();">
                    <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="返回{$menuinfo.name}列表" onclick="window.location.href='<?php echo U($menuinfo['model'].'/index','parentid=0&menuid='.$menuinfo[id].'&param='.ser($param).'&t=a');?>'">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

    <div  style="display:none;" id="cus_div_form">
        <div class="h_a">{$menuinfo.name}联系人{$aname}</div>
        <input type="hidden" name="id" value="{$parentid}">
        <input type="hidden" name="menuid" value="{$menuinfo['id']}">
        <input TYPE="hidden" name="param" value='<?php echo ser($param);?>'>
        <input type="hidden" name="t" id="t" value="{$t}">
        <input type="hidden" name="per_id" id="per_id" value="">
        <div class="table_full">
            <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                <tbody>
                <tr>
                    <th class="w10  aright">姓名{:C("separator");}</th>
                    <td class="w20"><input type="text" class="input  length_2" name="name" id="name"  value="{$pinfo.name}"><label class="red">*</label></td>
                    <th class="w10  aright">性别{:C('separator');}</th>
                    <td class="w20">{$sel:sex}</td>
                    <th class="w10 aright">生日{:C("separator");}</th>
                    <td class="w30"><input type="text" class="input  length_1" name="biryear" id="biryear"  value="{$pinfo.birthday|substr=0,4}" onclick="WdatePicker({dateFmt:'yyyy'});" readonly>
                        <input type="text" class="input  length_1" name="birday" id="birday"  value="{$pinfo.birthday|substr=5,5}" onclick="WdatePicker({dateFmt:'MM-dd'});" readonly></td>
                </tr>
                <tr>
                    <th class="w10  aright">部门{:C("separator");}</th>
                    <td class="w20"><input type="text" class="input  length_2" name="dept" id="dept"  value="{$pinfo.dept}"></td>
                    <th class="w10  aright">组别{:C('separator');}</th>
                    <td class="w20"><input type="text" class="input  length_2" name="cus_group" id="cus_group"  value="{$pinfo.cus_group}"></td>
                    <th class="w10  aright">职务{:C("separator");}</th>
                    <td class="w30"><input type="text" class="input  length_2" name="duty" id="duty"  value="{$pinfo.duty}"></td>
                </tr>
                <tr>
                    <th class="w10  aright">电话{:C('separator');}</th>
                    <td class="w20"><input type="text" class="input  length_2" name="tell" id="tell"  value="{$pinfo.tell}"></td>
                    <th  class="w10  aright">传真{:C("separator");}</th>
                    <td class="w20"><input type="text" class="input  length_2" name="fax" id="fax"  value="{$pinfo.fax}"></td>
                    <th  class="w10  aright">手机{:C('separator');}</th>
                    <td class="w30"><input type="text" class="input  length_2" name="phone" id="phone"  value="{$pinfo.phone}"></td>
                </tr>
                <tr>
                    <th  class="w10 aright">QQ{:C('separator');}</th>
                    <td class="w20"><input type="text" class="input  length_2" name="qq" id="qq"  value="{$pinfo.qq}"></td>
                    <th class="w10 aright">状态{:C("separator");}</th>
                    <td class="w20">{$sel:sts}</td>
                    <th  class="w10 aright">信用额度{:C('separator');}</th>
                    <td class="w30"><input type="text" class="input  length_2" name="cus_edu" id="cus_edu"  value="{$pinfo.cus_edu}"><label class="red">*</label></td>
                </tr>
                <tr>
                    <td colspan="6">
                        <?php if($menurole['a_m'] == 'Y') {?><button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">确定</button><?php }?>
                        <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="返回联系人列表" onclick="backcuslist();">
                        <!--<input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="返回客户资料列表" onclick="window.location.href='<?php echo U($menuinfo['model'].'/index','parentid=0&menuid='.$menuinfo[id].'&param='.ser($param).'&t=a');?>'">-->
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="btn_wrap">
    <div class="btn_wrap_pd">

    </div>
</div>
</form>
</div>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js?v"></script>
<script type="text/javascript" src="{$config_siteurl}statics/js/content_addtop.js"></script>
</body>
</html>
        <script type="text/javascript">
        function cusmodify(per_id){
            $("#cus_list").attr("class",'');
            $("#cus_form").attr("class",'current');

            $("#cus_div_list").css("display","none");
            $("#cus_div_form").css("display","");

           $('input[class^="input"]').val('');
           $('#sts').val("Y");
           $('#sex').val("女");

            $("#per_id").val(per_id);

            $.ajax({
                type:"POST",
                url:"{:U('Dcusperson/getcusper')}",
                data:'per_id='+per_id,
                dataType: "json",
                success:function(json){
                    $('#name').val(json.name);
                    $('#sex').val(json.sex);
                    $('#biryear').val(json.biryear);
                    $('#birday').val((json.birday) ? json.birday : '');
                    $('#dept').val(json.dept);
                    $('#cus_group').val(json.cus_group);
                    $('#duty').val(json.duty);
                    $('#tell').val(json.tell);
                    $('#fax').val(json.fax);
                    $('#phone').val(json.phone);
                    $('#qq').val(json.qq);
                    $('#sts').val(json.sts);
                    $('#cus_edu').val(json.cus_edu);
                }
            });


        }

        function backcuslist()
        {
            $("#cus_list").attr("class",'current');
            $("#cus_form").attr("class",'');

            $("#cus_div_list").css("display","");
            $("#cus_div_form").css("display","none");

            $('input[class^="input"]').val('');
            $('#sts').val("Y");
            $('#sex').val("女");
            $("#per_id").val('');
        }

        function backcusadd()
        {
            $("#cus_list").attr("class",'');
            $("#cus_form").attr("class",'current');

            $("#cus_div_list").css("display","none");
            $("#cus_div_form").css("display","");
        }
        </script>