<?php if (!defined('ZU_VERSION')) exit(); //print_r($menurole); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">

    <div class="h_a"><?php if($menurole['a_m'] == 'Y'){ ?><a href="<?php echo U($menuinfo['model'].'/add','parentid=0&menuid='.$menuinfo[id].'&t=a');?>" class="btn_big btn_submit mr10 J_ajax_submit_btn">增加{$menuinfo['name']}</a><?php }?></div>
        <div class="search_type cc mb10">
            <div class="mb10"> <span class="mr20">
                状态：
                <select class="select_2" name="status" id="status">
                    <option value='' <if condition="$obj:status eq ''">selected</if>>请选择</option>
                    <option value="1" <if condition="$obj:status eq '1'">selected</if>>显示</option>
                    <option value="2" <if condition="$obj:status eq '2'">selected</if>>不显示</option>
                </select>
                  名称：<input type="text" class="input length_2" name="q_name" id="q_name" size='10' value="{$obj:q_name}">
                  每页显示：<input type="text" class="input length_2" name="perpage" id="perpage" size='20' value="{$obj:perpage}">
                  <button class="btn" onclick="seachFun();">搜索</button>
                  </span>
            </div>
        </div>
        <div class="p10">
            <div class="pages" id="page_div"> {$Page} </div>
        </div>
            <div class="table_list">
                <table width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <td  width="20%">ID</td>
                        <td  width="20%" >名称</td>
                        <td  width="20%">状态</td>
                        <td  width="20%">排序</td>
                        <td  width="20%">操作 </td>
                    </tr>
                    </thead>
                    <tbody id="list-data">
                    <!--<volist name="custom" id="vo">
                        <tr>
                            <td align="center">{$vo.id}</td>
                            <td align="center">{$vo.name}</td>
                            <td align="center"><if condition="$vo['status'] eq '1'">显示</if>
                                <if condition="$vo['status'] eq '0'">不显示</if></td>
                            <td align="center">{$vo.sort}</td>
                            <td align="center"><a href="<?php// echo U($menuinfo['model'].'/add','id=\'.$vo['id'].\'&parentid=\'.$vo['parentid'].\'&menuid=\'.$menuinfo['id'].\'&t=m');?>">修改</a>
                                <if condition="$vo['parentid'] eq '0'"><a href="<?php// echo U($menuinfo['model'].'/add','parentid=\'.$vo['id'].\'&menuid=\'.$menuinfo['id'].\'&t=a');?>">&nbsp;&nbsp;增加子类</a></if></td>
                        </tr> -->
                        <!-- sub--->
                       <!-- <volist name="vo.sub" id="sub">
                            <tr>
                                <td align="center">&nbsp;</td>
                                <td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─{$sub.id}&nbsp;&nbsp;{$sub.name}</td>
                                <td align="center"><if condition="$sub['status'] eq '1'">显示</if>
                                    <if condition="$sub['status'] eq '0'">不显示</if></td>
                                <td align="center">{$vo.sort}</td>
                                <td align="center"><a href="<?php// echo U($menuinfo['model'].'/add','id=\'.$sub['id'].\'&parentid=\'.$sub['parentid'].\'&menuid=\'.$menuinfo['id'].\'&t=m');?>">修改</a></td>
                            </tr>
                        </volist>-->
                        <!-- sub--->
                    <!--</volist>-->
                    </tbody>
                </table>
                <div class="p10">
                    <div class="pages" id="page_div1"> {$Page} </div>
                </div>
            </div>

</div>
<input type="hidden" name="p" id="p" value='{$obj:p}'>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>
<script type="text/javascript">
    $(document).ready(function(){
        ajajPage($('#p').val());
    });
    function ajajPage(p){
        if(isNaN(p) || p =='') p=1;

        var param={};
        param.q_name =  $('#q_name').val();
        param.perpage = $('#perpage').val();
        param.status = $('#status').val();
        param.p = p;

        $.ajax({
            type: "POST",
            url: "{:U($menuinfo['model'].'/getlist')}",
            data: "&zu=85&p="+p+"&param="+encodeURIComponent(JSON2.stringify(param)),
            dataType: "json",
            beforeSend: function(){
                comload('list-data',5,'{$config_siteurl}',"{:C('LODINGTEXT')}");
            },
            success: function(json){
                if(json.error=='' || json.error==null || json.error==undefined){
                    $('#list-data').html('');

                    if(json.list.length == 0)
                    {
                        nodata('list-data',5,"{:C('NODATA')}");
                    }

                    $(json.list).each(function(){
                        //console.log(this);

                        trchangcolor('list-data');

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.id));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.name));

                        var stsname = (this.status == 1) ? '显示':'不显示';
                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(stsname));

                        td = tr.insertCell(-1);//
                        td.appendChild(document.createTextNode(this.sort));

                        //td = tr.insertCell(-1);//
                        //td.appendChild(document.createTextNode(this.sort));

                        td = tr.insertCell(-1);//
                        var id=this.id;
                        var parentid = this.parentid;

                        /*if('{$menurole.e_m}'=='Y'){
                            var a = document.createElement('a');
                            a.href = "{:U($menuinfo['model'].'/add')}&id="+id+"&parentid="+parentid+"&menuid="+{$menuinfo['id']}+"&t=m&param="+encodeURIComponent(JSON2.stringify(param));
                            a.style.cursor = 'pointer';
                            a.appendChild(document.createTextNode("{:C('edit')}"));

                            td.appendChild(a);
                            td.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'));
                        }

                        if('{$menurole.a_m}'=='Y'){
                            var a1 = document.createElement('a');
                            a1.href = "{:U($menuinfo['model'].'/add')}&parentid="+id+"&menuid="+{$menuinfo['id']}+"&t=a&param="+encodeURIComponent(JSON2.stringify(param));
                            a1.style.cursor = 'pointer';
                            a1.appendChild(document.createTextNode('\u00A0\u00A0\u00A0\u00A0'+"增加子类"));

                            td.appendChild(a1);
                        }*/

                        var div = document.createElement('div');
                            div.className = 'dropdown';

                        var p = document.createElement('p');
                            p._id = this.id;
                            p.appendChild(document.createTextNode("操作"));

                            p.onclick = function (){
                                var ucss =  'ul'+ this._id;


                                if(document.getElementById(ucss).style.display=="none"){
                                    document.getElementById(ucss).style.display = '';
                                }else{
                                    document.getElementById(ucss).style.display = 'none';
                                }

                            };


                         div.appendChild(p);

                        var ul = document.createElement("ul");
                            ul.id = 'ul' + this.id;
                            ul.style.display = 'none';

                        if('{$menurole.e_m}'=='Y'){
                            var li = document.createElement("li");
                            var lia = document.createElement('a');
                                lia.href = "{:U($menuinfo['model'].'/add')}&id="+id+"&parentid="+parentid+"&menuid="+{$menuinfo['id']}+"&t=m&param="+encodeURIComponent(JSON2.stringify(param));
                                lia.appendChild(document.createTextNode("{:C('edit')}"));
                             li.appendChild(lia);
                             ul.appendChild(li);
                        }

                        if('{$menurole.a_m}'=='Y'){
                            var li = document.createElement("li");
                            var lia = document.createElement('a');
                                lia.href = "{:U($menuinfo['model'].'/add')}&parentid="+id+"&menuid="+{$menuinfo['id']}+"&t=a&param="+encodeURIComponent(JSON2.stringify(param));
                                lia.appendChild(document.createTextNode("增加子类"));
                            li.appendChild(lia);
                            ul.appendChild(li);
                        }

                        div.appendChild(ul);

                        td.appendChild(div);




                        //sub
                        var sub = this.sub;
                        for(var j = 0; j< sub.length;j++){
                            var su = sub[j];

                            trchangcolor('list-data');

                            td = tr.insertCell(-1);//
                            td.appendChild(document.createTextNode('  '));

                            td = tr.insertCell(-1);//
                            td.appendChild(document.createTextNode('  └─ ' + su.id + '    ' + su.name + '   '));

                            var stsname = (su.status == 1) ? '显示':'不显示';
                            td = tr.insertCell(-1);//
                            td.appendChild(document.createTextNode(stsname));

                            td = tr.insertCell(-1);//
                            td.appendChild(document.createTextNode(su.sort));

                            td = tr.insertCell(-1);//
                            var id=su.id;
                            var parentid = su.parentid;


                           /* if('{$menurole.e_m}'=='Y'){
                                var a = document.createElement('a');
                                a.href = "{:U($menuinfo['model'].'/add')}&id="+id+"&parentid="+parentid+"&menuid="+{$menuinfo['id']}+"&t=m&param="+encodeURIComponent(JSON2.stringify(param));
                                a.style.cursor = 'pointer';
                                a.appendChild(document.createTextNode("{:C('edit')}"));
                                td.appendChild(a);
                            }*/

                            var div = document.createElement('div');
                            div.className = 'dropdown';

                            var p = document.createElement('p');
                            p._id = su.id;
                            p.appendChild(document.createTextNode("操作"));

                            p.onclick = function (){
                                var ucss =  'ul'+ this._id;


                                if(document.getElementById(ucss).style.display=="none"){
                                    document.getElementById(ucss).style.display = '';
                                }else{
                                    document.getElementById(ucss).style.display = 'none';
                                }

                            }

                            div.appendChild(p);

                            var ul = document.createElement("ul");
                            ul.id = 'ul' + su.id;
                            ul.style.display = 'none';

                            if('{$menurole.e_m}'=='Y'){
                                var li = document.createElement("li");
                                var lia = document.createElement('a');
                                lia.href = "{:U($menuinfo['model'].'/add')}&id="+id+"&parentid="+parentid+"&menuid="+{$menuinfo['id']}+"&t=m&param="+encodeURIComponent(JSON2.stringify(param));
                                lia.appendChild(document.createTextNode("{:C('edit')}"));
                                li.appendChild(lia);
                                ul.appendChild(li);
                            }

                            div.appendChild(ul);
                            td.appendChild(div);

                        }



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

    function showdata(json){

    }
</script>