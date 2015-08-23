<?php if (!defined('ZU_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
   <Admintemplate file="Common/Nav"/>
   <div class="table_list">
       <div class="p10">
           <div class="pages" id="page_div"> {$Page} </div>
       </div>
   <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td width="10%">序号</td>
            <td width="10%" align="left" >用户名</td>
            <td width="10%" align="left" >所属角色</td>
            <td width="10%"  align="left" >最后登录IP</td>
            <td width="10%"  align="left" >最后登录时间</td>
            <td width="15%"  align="left" >E-mail</td>
            <td width="20%">备注</td>
            <td width="15%" >管理操作</td>
          </tr>
        </thead>
        <tbody id="list-data">
        <!--<foreach name="Userlist" item="vo">
          <tr>
            <td width="10%" align="center">{$vo.id}</td>
            <td width="10%" >{$vo.username}</td>
            <td width="10%" >{$vo.role_name}</td>
            <td width="10%" >{$vo.last_login_ip}</td>
            <td width="10%"  >
            <if condition="$vo['last_login_time'] eq 0">
            该用户还没登陆过
            <else />
            {$vo.last_login_time|date="y-m-d H:i:s",###}
            </if>
            </td>
            <td width="15%">{$vo.email}</td>
            <td width="20%"  align="center">{$vo.remark}</td>
            <td width="15%"  align="center">
            <if condition="$User['username'] eq $vo['username']">
            <font color="#cccccc">修改</font> | 
            <font color="#cccccc">删除</font>
            <else />
            <a href="{:U("Management/edit",array("id"=>$vo[id]))}">修改</a> | 
            <a class="J_ajax_del" href="{:U('Management/delete',array('id'=>$vo['id']))}">删除</a>
            </if>
            </td>
          </tr>
         </foreach>-->
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
                var seachText = $('#seachText').val();
                var mid =$('#mid').val();
                //param.name = seachText;
                //param.id =  $('#menu_id').val();
                //param.perpage =  $('#perpage').val();
                param.status =1;
                param.p = p;
                //param.mid = mid;

                $.ajax({
                    type: "POST",
                    url: "{:U($menuinfo['model'].'/getlist')}",
                    data: "&zu=85&p="+p+"&param="+encodeURIComponent(JSON2.stringify(param)),
                    dataType: "json",
                    beforeSend: function(){
                        $('#list-data').html("");
                        tr = document.getElementById('list-data').insertRow(-1);//
                        tr.className = 'fontlist';

                        td = tr.insertCell(-1);//
                        td.colSpan = 8;
                        td.align = 'center';
                        td.className = 'acenter';
                        var img = document.createElement('img');
                        img.src='{$config_siteurl}statics/images/loading.gif';

                        td.appendChild(img);
                        td.appendChild(document.createTextNode("{:C('LODINGTEXT')}"));
                    },
                    success: function(json){
                       // console.log(json);
                        if(json.error=='' || json.error==null || json.error==undefined){
                            $('#list-data').html('');

                            if(json.list.length == 0)
                            {
                                tr = document.getElementById('list-data').insertRow(-1);//
                                tr.className = 'fontlist';

                                td = tr.insertCell(-1);//
                                td.colSpan = 8;
                                td.className = 'acenter';
                                td.appendChild(document.createTextNode('{:C('NODATA')}'));
                            }


                            $(json.list).each(function(){

                                if(navigator.userAgent.indexOf("MSIE")>0) {
                                    var bgcolor = '#66ff99';
                                    var bgcolor1 = '#ffffff';
                                    var bgcolor2 = '#ffff99';
                                }else{
                                    var bgcolor = 'rgb(102, 255, 153)';
                                    var bgcolor1 = 'rgb(255, 255, 255)';
                                    var bgcolor2 = 'rgb(255, 255, 153)';
                                }

                                tr = document.getElementById('list-data').insertRow(-1);//
                                tr.className = 'fontlist';

                                tr.onmouseover = function(){
                                    if(this.style.backgroundColor == bgcolor1){
                                        this.style.background='#ffff99';//rgb(255, 255, 153)
                                    }

                                }
                                tr.onmouseout = function(){
                                    if(this.style.backgroundColor != bgcolor){
                                        this.style.background='#ffffff';
                                    }
                                }

                                tr.onclick = function(){
                                    if(this.style.backgroundColor == bgcolor){
                                        this.style.background='#ffffff';//rgb(255, 255, 255)
                                    }else{
                                        this.style.background='#66ff99';//rgb(102, 255, 153)
                                    }
                                }

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.id));

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.username));

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.role_name));

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.last_login_ip));

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.last_login_time));

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.email));

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.remark));

                                td = tr.insertCell(-1);//
                                td.appendChild(document.createTextNode(this.status));

                                td = tr.insertCell(-1);//
                                var id=this.id;

                                if('{$operateqx.edit_manage}'=='Y'){
                                    var a = document.createElement('a');
                                    a.href = "{:U('Staff/edit')}&id="+id+"&param="+encodeURIComponent(JSON2.stringify(param));
                                    a.target='main';
                                    a.style.cursor = 'pointer';
                                    a.appendChild(document.createTextNode("{:L('edit')}"));
                                    td.appendChild(a);
                                }

                                if(this.login_acct == '{$Think.session.zuid}')
                                {
                                    var a1 = document.createElement('a');
                                    a1.href = "{:U('Staff/personset')}&id="+id+"&param="+encodeURIComponent(JSON2.stringify(param));
                                    a1.target='main';
                                    a1.style.cursor = 'pointer';
                                    a1.appendChild(document.createTextNode("{:L('set')}"));
                                    td.appendChild(a1);
                                }

                            });
                            $('#page_div').html(json.page);
                            $('#page_div1').html(json.page);
                            if(json.p <= json.totalp)
                            {
                                $('#p').val(json.p);
                            }else{
                                $('#p').val(json.totalp);
                            }
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

        </script>