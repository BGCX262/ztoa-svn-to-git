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
    <div class="h_a">{$menuinfo.name}{$aname}</div>
    <div class="pop_nav">
        <ul class="J_tabs_nav">
            <li class="current" id="cus_list"><a href="javascript:;">基本属性</a></li>
            <li class="" id="cus_form"><a href="javascript:;">初始化属性</a></li>
        </ul>
    </div>
    <form method="post" class="J_ajaxForm" action="{:U($menuinfo['model'].'/add')}">
        <input type="hidden" name="parentid" value="{$parentid}">
        <input type="hidden" name="id" value="{$info.customer_id}">
        <input type="hidden" name="menuid" value="{$menuinfo['id']}">
        <input TYPE="hidden" name="param" value='<?php echo ser($param);?>'>
        <input type="hidden" name="t" value="{$t}">
        <div class="J_tabs_contents">

            <div  style="display:;" id="cus_div_list">
                <div class="table_full"><!--  基础信息            -->
                    <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                        <tbody>
                        <tr>
                            <th class="w10 aright">线路代码{:C("separator");}<span class="closepng"></span></th>
                            <td class="w40"><input type="text" class="input  w90" name="customer" placeholder="(务必简称)" value="{$info.customer}"><label class="red">*</label></td>
                            <th  class="w10 aright">线路名称{:C('separator');}</th>
                            <td class="w40"><input type="text" class="input  w90" name="customer" placeholder="(务必简称)" value="{$info.customer}"><label class="red">*</label></td>
                        </tr>
                        <tr>
                            <th class="w10 aright">天数{:C("separator");}</th>
                            <td class="w40"><input type="text" class="input  w90" name="bank_name" value="{$info.bank_name}" placeholder="(全称--显示为出团通知书落款)"><label class="red">*</label></td>
                            <th  class="w10 aright">交通描述{:C('separator');}</th>
                            <td class="w40"><input type="text" class="input  w90" name="customer" placeholder="(务必简称)" value="{$info.customer}"><label class="red">*</label></td>
                        </tr>
                        <tr>
                            <td class="w100" colspan="4">
                                <!-- tab  start-->
                                <div id="page-wrap">
                                    <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="增加行程" onclick="addtab();">
                                    <div id="organic-tabs">
                                        <ul id="explore-nav">
                                            <li id="ex-featured"><a rel="featured" href="#" class="current"><b id="p-featured">第1天</b>&nbsp;<input type="button" class="closepng"></a></li>
                                        </ul>
                                        <div id="all-list-wrap">
                                            <ul id="featured" class="">
                                                <li>
                                                    <table border='0' class="w100">
                                                        <tr>
                                                            <td>区间<input type="text" class="input w80"></td>
                                                            <td>交通工具<input type="text" class="input w70"></td>
                                                            <td>班次<input type="text" class="input w80"></td>
                                                            <td>住宿<input type="text" class="input w80"></td>
                                                            <td><input type="checkbox" value="1">早<input type="checkbox" value="2">中<input type="checkbox" value="3">晚<input type="checkbox" value="4">自理</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan='5'><textarea  class="xctext" id="f12_LineDesc"></textarea></td>
                                                        </tr>
                                                    </table>

                                                </li>
                                            </ul>
                                        </div>
                                        <!-- END List Wrap -->
                                    </div>
                                    <!-- END Organic Tabs -->
                                </div>

                                <!-- tab end -->
                            </td>
                        </tr>


                        <tr>
                            <th class="w10 aright vcenter">服务标准{:C("separator");}</th>
                            <td class="w90" colspan="3"><textarea name="features" rows="2" cols="20" id="features_LineDesc" class="inputtext w90">{$info.features}</textarea></td>
                        </tr>
                        <tr>
                            <th class="w10 aright vcenter">温馨提醒{:C("separator");}</th>
                            <td class="w90" colspan="3"><textarea name="note" rows="2" cols="20" id="note_LineDesc" class="inputtext w90">{$info.note}</textarea></td>
                        </tr>
                        </tbody>
                    </table>
                </div><!--  基础信息            -->
            </div>

            <div  style="display:none;" id="cus_div_form">
                <div class="table_full">
                    <table cellpadding="0" cellspacing="0" class="table_form"  width="100%">
                        <tbody>
                        <tr>
                            <th class="w10 aright">开始日期{:C("separator");}</th>
                            <td class="w40"><input type="text" class="input  w95" name="customer" placeholder="线路全年不变，就是01-01，不填永久有效" value="{$info.customer}"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" readonly></td>
                            <th  class="w10 aright">成人价/返利{:C('separator');}</th>
                            <td class="w40"><input type="text" class="input  w95" name="customer" placeholder="" value="{$info.customer}"></td>
                        </tr>
                        <tr>
                            <th class="w10 aright">结束日期{:C("separator");}</th>
                            <td class="w40"><input type="text" class="input  w95" name="customer" placeholder="线路全年不变，就是12-31，不填永久有效" value="{$info.customer}"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" readonly></td>
                            <th  class="w10 aright">小孩价/返利{:C('separator');}</th>
                            <td class="w40"><input type="text" class="input  w95" name="customer" placeholder="(务必简称)" value="{$info.customer}"></td>
                        </tr>
                        <tr>
                            <th class="w10 aright">开班日期{:C("separator");}</th>
                            <td class="w90" colspan="3">
                                <input type="checkbox" id="sunday"><label for="sunday">周日</label>
                                <input type="checkbox" id="monday"><label for="monday">周一</label>
                                <input type="checkbox" id="tuesday"><label for="tuesday">周二</label>
                                <input type="checkbox" id="wednesday"><label for="wednesday">周三</label>
                                <input type="checkbox" id="thursday"><label for="thursday">周四</label>
                                <input type="checkbox" id="friday"><label for="friday">周五</label>
                                <input type="checkbox" id="saturday"><label for="saturday">周六</label>
                                <input type="checkbox" id="everyday"><label for="everyday">每天</label>
                                <input type="checkbox" id="specifyday"><label for="specifyday">指定日</label>
                            </td>
                        </tr>
                        <tr>
                            <th class="w10 aright"><a class="gys_dialog" href="<?php echo U('Dwtfperson/personlist');?>" title='供应商选择'>供应商{:C("separator");}</a></th>
                            <td class="w40" colspan="3">
                               <input type="hidden" name="wtf_id" id="wtf_id"><input type="text" class="input w10" id="wtfname" readonly="true">
                               部门<input type="text" class="input w5" name="dept" id="dept" value="{$info.dept}" readonly="true">
                               组别<input type="text" class="input  w5" name="wtf_group" id="wtf_group" value="{$info.wtf_group}" readonly="true">
                               联系人<input type="text" class="input  w5" name="wtf_name" id="wtf_name" value="{$info.wtf_name}" readonly="true">
                               电话<input type="text" class="input  w15" name="wtf_tell" id="wtf_tell" value="{$info.wtf_tell}" readonly="true">
                               传真<input type="text" class="input  w15" name="wtf_fax" id="wtf_fax" value="{$info.wtf_fax}" readonly="true">
                               手机<input type="text" class="input  w10" name="wtf_phone" id="wtf_phone" value="{$info.wtf_phone}" readonly="true">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="btn_wrap">
            <div class="btn_wrap_pd">
                <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">{$aname}</button>
                <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="返回" onclick="window.location.href='<?php echo U($menuinfo['model'].'/index','parentid=0&menuid='.$menuinfo[id].'&param='.ser($param).'&t=a');?>'">
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>
