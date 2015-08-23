<?php
//读取全部表名
$list_tables = D("Common")->list_tables();
?>
<table cellpadding="2" cellspacing="1" onclick="javascript:$('#minlength').val(0);$('#maxlength').val(255);">
    <tr> 
        <td>显示类型</td>
        <td><input name="setting[link_type]" type="radio" value="1"<?php if ($setting['link_type'] == 1) {
; ?> checked<?php }; ?> /> 自动显示<input name="setting[link_type]" type="radio" value="0"<?php if ($setting['link_type'] == 0) {
; ?> checked<?php }; ?> /> 搜索显示</td>
    </tr>
    <tr> 
        <td>关联表名</td>
        <td>
                <?php if (is_array($list_tables)) { ?>   
                <select name="setting[table_name]" id="st_name">
                    <?php
                    foreach ($list_tables as $names) {
                        if ($names == $setting['table_name'])
                            $select = 'selected';
                        else
                            $select = '';
                        ?>
                        <option value='<?php echo $names ?>' <?php echo $select ?>><?php echo $names ?></option>
    <?php } ?>
                </select>
<?php } ?>

            <script type="text/javascript">
                <!--
                $(document).ready(function() {

                    updatemenu($('#st_name').val(),true);
	
                    $("#st_name").change(function() {
                        updatemenu($(this).trigger("select").val(),false);
                    });

                    function updatemenu(table_name,seltype) { 
                        $.getJSON('api.php?m=Ajax_linkfield&act=search_data&key=<?=urlencode(authcode("true","",C("AUTHCODE"),3600));?>&callback=?', {tables: table_name,random:Math.random()}, function(data){
                            if (data != null) {
                                update(data,'set_id','<?php echo $setting['set_id']; ?>',seltype);
                                update(data,'set_title','<?php echo $setting['set_title']; ?>',seltype);
                            }else{
                                alert('数据查询错误！');
                            }
                        });
			
                    }
                    function update(data,rid,riddata,seltype) {
                        var str = selected = '';
                        for(var i in data){
				
                            if(seltype){
                                if (i == riddata) {
                                    selected ='selected';
                                }else{
                                    selected ='';
                                }
                            }else{
                                selected ='';
                            }
                            str += '<option '+selected+'>'+i+'</option>';
                        }
                        $('#'+rid).html(str);
                    }	
                });
                //-->
            </script>
        </td>
    </tr>
    <tr> 
        <td>查询字段</td>
        <td><input type="text" name="setting[select_title]" id="setting[select_title]" value="<?php echo $setting['select_title']; ?>" size="40" class="input-text"> 请填写字段名如：id,title （为空则表示全部查询）</td>
    </tr>
    <tr> 
        <td>like字段</td>
        <td><input name="setting[like_title]" type="text" class="input-text" id="setting[like_title]" value="<?php echo $setting['like_title']; ?>" size="40"> 请填写字段名如：title（解读为where 'title' like'%张三%'）</td>
    </tr>
    <tr> 
        <td>赋值字段</td>
        <td><select name="setting[set_title]" id="set_title"></select> 用于返回值赋值给管理字段。</td>
    </tr>
    <tr> 
        <td>主键</td>
        <td><select name="setting[set_id]" id="set_id"></select> 用于返回值赋值给管理字段。(表里面唯一标示，比如主键)</td>
    </tr>
    <tr> 
        <td>存入数据方式</td>
        <td><input type="radio" name="setting[insert_type]" value="id" <?php if ($setting['insert_type'] == 'id') echo 'checked'; ?> />ID存入<input type="radio" name="setting[insert_type]" value="title" <?php if ($setting['insert_type'] == 'title') echo 'checked'; ?> />标题存入<input type="radio" name="setting[insert_type]" value="title_id" <?php if ($setting['insert_type'] == 'title_id') echo 'checked'; ?> />标题+id存入</td>
    </tr>
</table>