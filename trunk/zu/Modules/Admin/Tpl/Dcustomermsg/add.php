<?php if (!defined('ZU_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap jj">
    <div class="common-form">
        <form method="post" class="J_ajaxForm" action="{:U($menuinfo['model'].'/add')}">
            <input type="hidden" name="parentid" value="{$parentid}">
            <input type="hidden" name="id" value="{$info.customer_id}">
            <input type="hidden" name="menuid" value="{$menuinfo['id']}">
            <input TYPE="hidden" name="param" value='<?php echo ser($param);?>'>
            <input type="hidden" name="t" value="{$t}">
           <!-- <input type="hidden" name="opr_no" value="{$info.opr_no}">
            <input type="hidden" name="opr_time" value="{$info.opr_time}">-->
            <div class="h_a">{$menuinfo.name}{$aname}</div>
            <div class="table_full">
                <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                    <tbody>
                    <tr>
                        <th class="w10 aright">客户简称{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input  length_4" name="customer" placeholder="(务必简称)" value="{$info.customer}"><label class="red">*</label></td>
                        <th  class="w10 aright">分组{:C('separator');}</th>
                        <td class="w40">{$sel:cus_group}</td>
                    </tr>
                    <tr>
                        <th class="w10 aright">客户户名{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input  length_4" name="bank_name" value="{$info.bank_name}" placeholder="(全称--显示为出团通知书落款)"><label class="red">*</label></td>
                        <th  class="w10 aright">类型{:C('separator');}</th>
                        <td class="w40">{$sel:cus_type}</td>
                    </tr>
                    <tr>
                        <th class="w10 aright">对公账号{:C("separator");}</th>
                        <td class="w90" colspan="3"><input type="text" class="input length_4" name="cardno" value="{$info.cardno}"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">开户行{:C("separator");}</th>
                        <td class="w90" colspan="3"><input type="text" class="input length_4" name="oc_bank" value="{$info.oc_bank}" placeholder="(全称)"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">所在地区{:C("separator");}</th>
                        <td class="w90" colspan="3"><select name="province"></select><select name="city"></select><select name="area"></select></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">街道地址{:C("separator");}</th>
                        <td class="w90" colspan="3"><input type="text" class="input  length_4" name="address" value="{$info.address}" placeholder="不需要重复填写省/市/区" style="width: 260px;">   靠近
                            <input type="text" class="input" name="road" value="{$info.road}"  placeholder="斜西XX街"></td>
                    </tr>
                    <tr>
                        <th class="w10 aright">邮编{:C("separator");}</th>
                        <td class="w40"><input type="text" class="input  length_4" name="postno" value="{$info.postno}"></td>
                        <th  class="w10 aright">合作状态{:C('separator');}</th>
                        <td class="w40">{$sel:hzstate}</td>
                    </tr>
                    <tr>
                        <th class="w10 aright">结算方式{:C("separator");}</th>
                        <td class="w40">{$sel:jsstate}</td>
                        <th  class="w10 aright">团质{:C('separator');}</th>
                        <td class="w40">{$sel:tzlevel}</td>
                    </tr>
                    <tr>
                        <th class="w10 aright">付款信用{:C("separator");}</th>
                        <td class="w40">{$sel:xylevel}</td>
                        <th  class="w10 aright">客流量{:C('separator');}</th>
                        <td class="w40"><input type="text" class="input  length_4" name="kll" value="{$info.kll}"></td>
                    </tr>

                    <tr>
                        <th class="w10 aright vcenter">特色{:C("separator");}</th>
                        <td class="w90" colspan="3"><textarea name="features" rows="2" cols="20" id="features_LineDesc" class="inputtext w90">{$info.features}</textarea></td>
                    </tr>
                    <tr>
                        <th class="w10 aright vcenter">收款提醒{:C("separator");}</th>
                        <td class="w90" colspan="3"><textarea name="note" rows="2" cols="20" id="note_LineDesc" class="inputtext w90">{$info.note}</textarea></td>
                    </tr>
                    </tbody>
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