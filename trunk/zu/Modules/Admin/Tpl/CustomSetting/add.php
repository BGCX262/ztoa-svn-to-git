<?php if (!defined('ZU_VERSION')) exit(); //dump(json_decode($param)); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap jj">
    <div class="common-form">
        <form method="post" class="J_ajaxForm" action="{:U($menuinfo['model'].'/add')}">
            <input type="hidden" name="parentid" value="{$parentid}">
            <input type="hidden" name="id" value="{$info.id}">
            <input type="hidden" name="menuid" value="{$menuinfo['id']}">
            <input TYPE="hidden" name="param" value='<?php echo ser($param);?>'>
            <input type="hidden" name="t" value="{$t}">
            <div class="h_a">{$menuinfo.name}{$aname}</div>
            <div class="table_list">
                <table cellpadding="0" cellspacing="0" class="table_form" width="100%">
                    <tbody>
                    <tr>
                        <td>名称:</td>
                        <td><input type="text" class="input" name="name" value="{$info.name}"></td>
                    </tr>
                    <tr>
                        <td>排序:</td>
                        <td><input type="text" class="input" name="sort" id="sort" value="{$info.sort}"></td>
                    </tr>
                    <tr>
                        <td>状态:</td>
                        <td><select name="status">
                            <option value="1" <eq name="info.status" value="1">selected</eq>>显示</option>
                            <option value="2" <eq name="info.status" value="2">selected</eq>>不显示</option>
                        </select></td>
                    </tr>

                    </tbody>
                </table>
            </div>
            <div class="">
                <div class="btn_wrap_pd">
                    <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">{$aname}</button>
                    <!--<a href="<?php //echo U($menuinfo['model'].'/customlist','parentid=0&menuid='.$menuinfo[id].'&t=a');?>&param='<?php //echo base64_encode(serialize(json_decode($param)));?>'">back</a>-->
                    <input type="button" class="btn btn_submit mr10 J_ajax_submit_btn" value="返回" onclick="window.location.href='<?php echo U($menuinfo['model'].'/customlist','parentid=0&menuid='.$menuinfo[id].'&param='.ser($param).'&t=a');?>'">
                </div>
            </div>
        </form>
    </div>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>