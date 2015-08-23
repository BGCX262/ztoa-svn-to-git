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
            <li class="current" id="cus_list"><a href="javascript:;" onclick="javascript:backcuslist();">{$menuinfo.name}银行帐号列表</a></li>
            <li class="" id="cus_form"><a href="javascript:;" onclick="javascript:backcusadd();">{$menuinfo.name}银行帐号{$aname}</a></li>
        </ul>
    </div>
    <form class="J_ajaxForm" name="myform" id="myform" action="{:U('Dwtfzhmsg/personsave')}" method="post">
        <div class="J_tabs_contents">

            <div  style="display:;" id="cus_div_list">
                <div class="h_a">{$menuinfo.name}银行帐号列表</div>
                <div class="table_full">
                    <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                        <tr>
                            <td class="w20 ">户名</td>
                            <td class="w20">开户行</td>
                            <td  class="w20 ">帐号</td>
                            <td class="w20">默认帐号</td>
                            <td class="w10">状态</td>
                            <td  class="w10">操作</td>
                        </tr>
                        <tbody id="data-list">
                        <foreach name="info" item="vo">
                            <tr>
                                <th class="w20 ">{$vo.wname}</th>
                                <td class="w20">{$vo.wbank_name}</td>
                                <th  class="w20 ">{$vo.wbank_zh}</th>
                                <td class="w20"><?php echo ($vo['default_zh'] == '1') ? '是' : '否'; ?></td>
                                <th  class="w10"><?php echo ($vo['sts']=='Y') ? '停用' : '使用'; ?></th>
                                <td class="w10"> <?php if($menurole['e_m'] == 'Y') {?>
                                    <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="修改" onclick="cusmodify(<?php echo $vo['id'];?>);">
                                    <?php }?>
                                </td>
                            </tr>
                        </foreach>
                        <tr>
                            <td colspan="5">
                                <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="增加银行帐号" onclick="backcusadd();">
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
                            <th class="w10  aright">户名{:C("separator");}</th>
                            <td class="w90"><input type="text" class="input  length_6" name="wname" id="wname"  value="{$pinfo.wname}"><label class="red">*</label></td>
                        </tr>
                        <tr>
                            <th class="w10  aright">开户行{:C("separator");}</th>
                            <td class="w90"><input type="text" class="input  length_6" name="wbank_name" id="wbank_name"  value="{$pinfo.wbank_name}"><label class="red">*</label></td>
                        </tr>
                        <tr>
                            <th class="w10  aright">帐号{:C('separator');}</th>
                            <td class="w90"><input type="text" class="input  length_6" name="wbank_zh" id="wbank_zh"  value="{$pinfo.wbank_zh}"><label class="red">*</label></td>
                        </tr>
                        <tr>
                            <th  class="w10 aright">默认主帐号{:C('separator');}</th>
                            <td class="w90"><input type="radio" class="input" name="default_zh" id="default_zh1"  value="1" <?php if($pinfo['default_zh']==1) echo 'checked'; ?>><label  for="default_zh1">是</label>
                                <input type="radio" class="input" name="default_zh" id="default_zh2"  value="2" <?php if($pinfo['default_zh']==2) echo 'checked'; ?> ><label for="default_zh2">否</label></td>
                        </tr>
                        <tr>

                            <th class="w10 aright">状态{:C("separator");}</th>
                            <td class="w90">{$sel:sts}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
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

        $('input[class^="input  length_6"]').val('');
        $('#sts').val("N");
        $('#default_zh2').attr("checked",true);

        $("#per_id").val(per_id);

        $.ajax({
            type:"POST",
            url:"{:U('Dwtfzhmsg/getcusper')}",
            data:'per_id='+per_id,
            dataType: "json",
            success:function(json){
                $('#wname').val(json.wname);
                $('#wbank_name').val(json.wbank_name);
                $('#wbank_zh').val(json.wbank_zh);
                if(json.default_zh == 1){
                    $("#default_zh1").attr("checked",true)
                }else{
                    $("#default_zh2").attr("checked",true)
                }

                $('#sts').val(json.sts);
            }
        });


    }

    function backcuslist()
    {
        $("#cus_list").attr("class",'current');
        $("#cus_form").attr("class",'');

        $("#cus_div_list").css("display","");
        $("#cus_div_form").css("display","none");

        $('input[class^="input  length_6"]').val('');
        $('#sts').val("N");
        $('#default_zh2').attr("checked",true);
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