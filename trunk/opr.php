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

<div class="wrap jj">
    <div class="pop_nav">
        <ul class="J_tabs_nav">
            <li class="current"><a href="javascript:;;">基本属性</a></li>
            <li class=""><a href="javascript:;;">选项设置</a></li>
            <li class=""><a href="javascript:;;">模板设置</a></li>
            <li class=""><a href="javascript:;;">权限设置</a></li>
        </ul>
    </div>
    <div class="common-form">
        <form method="post" class="J_ajaxForm" action="{:U('Cusperson/personsave')}">
            <input type="hidden" name="id" value="{$parentid}">
            <input type="hidden" name="menuid" value="{$menuinfo['id']}">
            <input TYPE="hidden" name="param" value='<?php echo ser($param);?>'>
            <input type="hidden" name="t" value="{$t}">
            <div class="h_a">{$menuinfo.name}{$aname}</div>
            <div class="table_full">
                <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                    <tbody>
                    <tr>
                        <th class="w10 ">姓名{:C("separator");}</th>
                        <td class="w10">性别{:C('separator');}</td>
                        <th  class="w10 ">部门{:C("separator");}</th>
                        <td class="w10">组别{:C('separator');}</td>
                        <th class="w10 ">职务{:C("separator");}</th>
                        <td class="w10">电话{:C('separator');}</td>
                        <th  class="w10 ">传真{:C("separator");}</th>
                        <td class="w10">手机{:C('separator');}</td>
                    </tr>
                    <tr>
                        <th class="w10 "><input type="text" class="input  length_2" name="name"  value="{$pinfo.name}"></th>
                        <td class="w10">{$sel:sex}</td>
                        <th  class="w10 "><input type="text" class="input  length_2" name="dept"  value="{$pinfo.dept}"></th>
                        <td class="w10"><input type="text" class="input  length_2" name="cus_group"  value="{$pinfo.cus_group}"></td>
                        <th class="w10 "><input type="text" class="input  length_2" name="duty"  value="{$pinfo.duty}"></th>
                        <td class="w10"><input type="text" class="input  length_2" name="tell"  value="{$pinfo.tell}"></td>
                        <th  class="w10"><input type="text" class="input  length_2" name="fax"  value="{$pinfo.fax}"></th>
                        <td class="w10"><input type="text" class="input  length_2" name="phone"  value="{$pinfo.phone}"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">生日{:C("separator");}</th>
                        <td class="w10"><input type="text" class="input  length_1" name="biryear"  value="{$pinfo.birthday|substr=0,4}" onclick="WdatePicker({dateFmt:'yyyy'});" readonly>
                            <input type="text" class="input  length_1" name="birday"  value="{$pinfo.birthday|substr=5,5}" onclick="WdatePicker({dateFmt:'MM-dd'});" readonly></td>
                        <th  class="w10 aright">QQ{:C('separator');}</th>
                        <td class="w10"><input type="text" class="input  length_2" name="qq"  value="{$pinfo.qq}"></td>
                        <th class="w10 aright">状态{:C("separator");}</th>
                        <td class="w10">{$sel:sts}</td>
                        <th  class="w10 aright">信用额度{:C('separator');}</th>
                        <td class="w10"><input type="text" class="input  length_2" name="cus_edu"  value="{$pinfo.cus_edu}"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="">
                <div class="btn_wrap_pd">
                    <?php if($menurole['a_m'] == 'Y') {?><button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">确定</button><?php }?>
                    <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="返回" onclick="window.location.href='<?php echo U($menuinfo['model'].'/index','parentid=0&menuid='.$menuinfo[id].'&param='.ser($param).'&t=a');?>'">
                </div>
            </div>
        </form>
    </div>

    <!-- list start -->
    <div class="common-form" style="display: none;">
        <div class="h_a">{$menuinfo.name}联系人列表</div>
        <div class="table_full">
            <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                <tbody>
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
                            <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="修改" onclick="window.location.href='<?php echo U('Cusperson/personadd','id='.$parentid.'&pid='.$vo[per_id].'&menuid='.$menuinfo[id].'&param='.ser($param).'&t=m');?>'">
                            <?php }?>
                        </td>
                    </tr>
                </foreach>
                </tbody>
            </table>
        </div>

    </div>
    <!-- list end -->
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>