<?php if (!defined('ZU_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap jj">
    <div class="common-form">
        <form method="post" class="J_ajaxForm" action="{:U($menuinfo['model'].'/add')}">
            <input type="hidden" name="parentid" value="{$parentid}">
            <input type="hidden" name="id" value="{$info.wtf_id}">
            <input type="hidden" name="menuid" value="{$menuinfo['id']}">
            <input TYPE="hidden" name="param" value='<?php echo ser($param);?>'>
            <input type="hidden" name="t" value="{$t}">
            <div class="h_a">{$menuinfo.name}{$aname}</div>
            <div class="table_full">
                <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                    <tr>
                        <th class="w10 aright">供应商简称{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input  w90" name="wtf_name" placeholder="(务必简称)" value="{$info.wtf_name}"><label class="red">*</label></td>
                        <th  class="w10 aright">类型{:C('separator');}</th>
                        <td class="w40">{$sel:type_code}</td>
                    </tr>
                    <tr>
                        <th class="w10 aright">供应商户名{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input w90" name="bank_name" value="{$info.bank_name}" placeholder="(全称--显示为出团通知书落款)"><label class="red">*</label></td>
                        <th  class="w10 aright">状态{:C('separator');}</th>
                        <td class="w40">{$sel:dis_sts}</td>
                    </tr>
                    <tr>
                        <th class="w10 aright">对公账号{:C("separator");}</th>
                        <td class="w40" ><input type="text" class="input w90" name="cardno" value="{$info.cardno}"></td>
                        <th  class="w10 aright">我社比例{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  w90" name="bili" value="{$info.bili}"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">开户行{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input w90" name="oc_bank" value="{$info.oc_bank}" placeholder="(全称)"></td>
                        <th  class="w10 aright">排序{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  w90" name="wsort" value="{$info.wsort}"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">所在地区{:C("separator");}</th>
                        <td class="w90" colspan="3"><select name="province"></select><select name="city"></select><select name="area"></select></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">街道地址{:C("separator");}</th>
                        <td class="w90" colspan="3"><input type="text" class="input  w60" name="address" value="{$info.address}" placeholder="不需要重复填写省/市/区">   靠近
                            <input type="text" class="input w30" name="road" value="{$info.road}"  placeholder="斜西XX街"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">邮编{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input  w90" name="postno" value="{$info.postno}"></td>
                        <th class="w10 aright">结算方式{:C("separator");}</th>
                        <td class="w40">{$sel:jsstate}</td>
                    </tr>
                    <tr>
                        <th  class="w10 aright">等级{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  w90" name="level" value="{$info.level}"></td>
                        <th  class="w10" colspan="2"><input type="checkbox" id="chkbox" onclick="openclose('gaoji');"><label for="chkbox" class="blue">【显示/隐藏】高级选项</label></th>
                    </tr>
                  <!-- 高级选项开始-->
                    <tbody id="gaoji" class="disnone">
                    <tr>
                        <th class="w10 aright">企业法人营业执照{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input  w90" name="fr_no" value="{$info.fr_no}"></td>
                        <th  class="w10 aright">经营许可证号{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  w90" name="jy_no" value="{$info.jy_no}"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">组织机构代码证号{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input  w90" name="group_no" value="{$info.group_no}" ></td>
                        <th  class="w10 aright">税务登记证号{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  w90" name="sw_no" value="{$info.sw_no}"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">旅行社责任保险{:C("separator");}</th>
                        <td class="w40" ><input type="text" class="input w90" name="lxsbx" value="{$info.lxsbx}"></td>
                        <th  class="w10 aright">注册资金{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  w90" name="zc_money" value="{$info.zc_money}"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">法人代表姓名{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input w90" name="fr_name" value="{$info.fr_name}"></td>
                        <th  class="w10 aright">身份证号码{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  w90" name="cardno" value="{$info.cardno}"></td>
                    </tr>
                    </tbody>
                  <!-- 高级选项结束-->
                    <tr>
                        <th class="w10 aright vcenter">特色{:C("separator");}</th>
                        <td class="w90" colspan="3"><textarea name="features" id="features_LineDesc" class="inputtext w90">{$info.features}</textarea></td>
                    </tr>
                    <tr>
                        <th class="w10 aright vcenter">备注{:C("separator");}</th>
                        <td class="w90" colspan="3"><textarea name="note" rows="2" cols="20" id="note_LineDesc" class="inputtext w90">{$info.note}</textarea></td>
                    </tr>

                </table>
            </div>
            <div class="">
                <div class="btn_wrap_pd">
                    <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">{$aname}</button>
                    <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="返回" onclick="window.location.href='<?php echo U($menuinfo['model'].'/index','parentid=0&menuid='.$menuinfo[id].'&param='.ser($param).'&t=a');?>'">
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>
<script language="javascript" defer>
    new PCAS("province","city","area","{$info.province}","{$info.city}","{$info.area}");
</script>