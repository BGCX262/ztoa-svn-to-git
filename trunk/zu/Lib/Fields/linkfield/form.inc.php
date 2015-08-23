<?php

function linkfield($field, $value, $fieldinfo) {
    extract($fieldinfo);
    $setting = unserialize($setting);

    if ($setting['link_type']) {

        $get_db = M("");

        $sel_tit = $setting['select_title'] ? $setting['select_title'] : '*';

        $sql = "SELECT " . $sel_tit . " FROM `" . $setting['table_name'] . "`";

        $dataArr = $get_db->query($sql);

        $value = str_replace('&amp;', '&', $value);
        $data = '<select name="info[' . $fieldinfo['field'] . ']" id="' . $fieldinfo['field'] . '"><option>请选择</option>';

        foreach ($dataArr as $v) {
            if ($setting['insert_type'] == "id") {
                $output_type = $v[$setting['set_id']];
            } elseif ($setting['insert_type'] == "title") {
                $output_type = $v[$setting['set_title']];
            } else {
                $output_type = $v[$setting['set_title']] . '_' . $v[$setting['set_id']];
            }
            if ($output_type == $value)
                $select = 'selected';
            else
                $select = '';
            $data .= "<option value='" . $output_type . "' " . $select . ">" . $v[$setting['set_title']] . "</option>\n";
        }
        $data .= '</select>';
    }else {
        $key = urlencode(authcode("true","",C("AUTHCODE"),3600));
        $domain = CONFIG_SITEURL_MODEL;
        $data = <<<EOT
            <style type="text/css">
            .content_div{ margin-top:0px; font-size:14px; position:relative}
            #search_div{$field}{ position:absolute; top:23px; border:1px solid #dfdfdf; text-align:left; padding:1px; left:0px;*left:0px; width:263px;*width:260px; background-color:#FFF; display:none; font-size:12px;}
            #search_div{$field} li{ line-height:24px;cursor:pointer}
            #search_div{$field} li a{  padding-left:6px;display:block}
            #search_div{$field} li a:hover, #search_div{$field} li:hover{ background-color:#e2eaff}
            </style>
            <div class="content_div">
                <input type="text" size="41" id="cat_search{$field}" value="" onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;" class='input'><input name="info[{$fieldinfo['field']}]" id="{$fieldinfo['field']}" type="hidden" class='input' value="{$value}" size="41"/>
                <ul id="search_div{$field}"></ul>
            </div>		
            <script type="text/javascript" language="javascript" >
                function setvalue{$field}(title,id)
                {
                    var title = title;
                    var id = id;
                    var type = "{$setting['insert_type']}";
                    if(type == "id")
                    {
                        $("#{$fieldinfo['field']}").val(id);
                    }
                    else if(type == "title")
                    {
                        $("#{$fieldinfo['field']}").val(title);
                    }
                    else if(type == "title_id")
                    {
                        $("#{$fieldinfo['field']}").val(title+'|'+id);
                    }
                    $("#cat_search{$field}").val(title);
                    $('#search_div{$field}').hide();
                }
				
            $(document).ready(function(){
				if($("#{$fieldinfo['field']}").val().length > 0){
				
					var value = $("#{$fieldinfo['field']}").val();
					var tablename = '{$setting['table_name']}';
					var set_title = '{$setting['set_title']}';
					var set_id = '{$setting['set_id']}';
					var set_type = '{$setting['insert_type']}';
					$.getJSON('{$domain}api.php?m=Ajax_linkfield&a=public_index&act=check_search&key={$key}&callback=?', {value: value,table_name: tablename,set_title: set_title,set_id: set_id,set_type: set_type,random:Math.random()}, function(data2){
						if (data2 != null) {
							$.each(data2, function(i,n){				
							$('#cat_search{$field}').val(n.{$setting['set_title']});
							});
						} else {
							$('#search_div{$field}').hide();
						}
					});
					
				}

				$('#cat_search{$field}').keyup(function(){
					var value = $("#cat_search{$field}").val();
					var tablename = '{$setting['table_name']}';
					var select_title = '{$setting['select_title']}';
					var like_title = '{$setting['like_title']}';
					var set_title = '{$setting['set_title']}';
					var set_id = '{$setting['set_id']}';
					
					if (value.length > 0){
						$.getJSON('{$domain}api.php?m=Ajax_linkfield&a=public_index&act=search_ajax&key={$key}&callback=?', {value: value,table_name: tablename,select_title: select_title,like_title: like_title,set_title: set_title,set_id: set_id,limit: 20,random:Math.random()}, function(data){
							if (data != null) {
								var str = '';
								$.each(data, function(i,n){
									str += '<li onclick=\'setvalue{$field}("'+n.{$setting['set_title']}+'","'+n.{$setting['set_id']}+'");\'>'+n.{$setting['set_title']}+'</li>';
								});
								$('#search_div{$field}').html(str);
								$('#search_div{$field}').show();
							} else {
								$('#search_div{$field}').hide();
							}
						});
					} else {
						$('#search_div{$field}').hide();
					} 
				});	
            })
            </script>
EOT;
    }
    return $data;
}

?>