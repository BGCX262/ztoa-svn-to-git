<?php if (!defined('ZU_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">

    <div class="h_a"><?php if($menurole['a_m'] == 'Y'){ ?><a href="<?php echo U($menuinfo['model'].'/add','parentid=0&menuid='.$menuinfo[id].'&t=a');?>" class="btn_big btn_submit mr10 J_ajax_submit_btn">增加{$menuinfo['name']}</a><?php }?></div>

    <div class="search_type cc mb10">
        <div class="mb10"> <span class="mr20">
    区域：
    <select name="province" id="province"></select><select name="city" id="city"></select><select name="area" id="area"></select>
      地址：<input type="text" class="input length_2" name="address" id="address" size='20' value="{$obj:q_address}">
       每页显示：<input type="text" class="input length_2" name="perpage" id="perpage" size='20' value="{$obj:perpage}">
      <button class="btn"  onclick="seachFun();">搜索</button>

      </span> </div>
    </div>

    <div class="p10">
        <div class="pages" id="page_div"> {$Page} </div>
    </div>

    <form class="J_ajaxForm" action="{:U('Menu/listorders')}" method="post">
        <div class="table_list">
            <table width="100%" cellspacing="0">
                <thead>
                <tr>
                    <td width="30">客户简称</td>
                    <td width="50" >省份</td>
                    <td width="60">城市</td>
                    <td width="60">区域</td>
                    <td width="60">地址</td>
                    <td width="60">靠近</td>
                    <td width="60">邮编</td>
                    <td width="60">状态</td>
                    <td width="60">结算方式</td>
                    <td width="60">等级</td>
                    <td width="60">录入人</td>
                    <td width="60">操作</td>
                </tr>
                </thead>
                <tbody id="list-data">
                <!--<volist name="custom" id="vo">
                        <tr>
                            <td align="center">{$vo.id}</td>
                            <td align="center">{$vo.name}</td>
                            <td align="center"><a href="<?php// echo U('Custom/add','id=\'.$vo['id'].\'&parentid=\'.$vo['parentid'].\'&t=m');?>">修改</a>
                                <if condition="$vo['parentid'] eq '0'"><a href="<?php //echo U('Custom/add','parentid=\'.$vo['id'].\'&t=a');?>">&nbsp;&nbsp;增加子类</a></if></td>
                        </tr>
                    </volist>-->
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
    param.q_province =  $('#province').val();
    param.q_city =  $('#city').val();
    param.q_area =  $('#area').val();
    param.q_address =  $('#address').val();
    param.perpage = $('#perpage').val();
    param.status = $('#status').val();
    param.p = p;

    $.ajax({
        type: "POST",
        url: "{:U($menuinfo['model'].'/getlist')}",
        data: "&zu=85&p="+p+"&param="+encodeURIComponent(JSON2.stringify(param)),
        dataType: "json",
        beforeSend: function(){
            comload('list-data',11,'{$config_siteurl}',"{:C('LODINGTEXT')}");
        },
        success: function(json){

            if(json.error=='' || json.error==null || json.error==undefined){
                $('#list-data').html('');

                if(json.list.length == 0)
                {
                    nodata('list-data',11,"{:C('NODATA')}");
                }

                $(json.list).each(function(){
                    //console.log(this);

                    trchangcolor('list-data');

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.wtf_name));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.province));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.city));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.area));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.address));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.road));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.postno));

                    var stsname = (this.dis_sts == 'Y') ? '常用':'备用';
                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(stsname));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.jsstate));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.level));

                    td = tr.insertCell(-1);//
                    td.appendChild(document.createTextNode(this.opr_no));


                    td = tr.insertCell(-1);//
                    var id=this.wtf_id;
                    var parentid = this.parentid;



                    /* var div = document.createElement('div');
                    div.className = 'dropdown';

                    var p = document.createElement('p');
                    p._id = this.customer_id;
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
                    ul.id = 'ul' + this.customer_id;
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
                        lia.href = "{:U('Dcusperson/personadd')}&id="+id+"&menuid="+{$menuinfo['id']}+"&t=a&param="+encodeURIComponent(JSON2.stringify(param));
                        lia.appendChild(document.createTextNode("联系人列表 ("+this.subcount +")"));
                        li.appendChild(lia);
                        ul.appendChild(li);
                    }

                    div.appendChild(ul);

                    td.appendChild(div);*/

                    var ul = document.createElement("ul");
                    ul.className = 'nav-alipay';

                    var li = document.createElement("li");
                    li.className = "nav-item business";

                    var lia = document.createElement("a");
                    lia.className = "nav-item-link";
                    lia.appendChild(document.createTextNode("操作"));
                    lia.href = "#";

                    li.appendChild(lia);

                    var div = document.createElement("div");
                    div.className = "nav-item-sub sl-shadow";

                    var divtable = document.createElement("table");
                    divtable.className = "nav-item-table";

                    divtr = divtable.insertRow(-1);

                    //th = divtr.appendChild(document.createElement("th"));//
                    //th.appendChild(document.createTextNode("财务类"));

                    //th = divtr.appendChild(document.createElement("th"));//
                    //th.appendChild(document.createTextNode("行程上"));

                    //th = divtr.appendChild(document.createElement("th"));//
                    //th.appendChild(document.createTextNode("行程前"));

                    th = divtr.appendChild(document.createElement("th"));//
                    th.className = 'last';
                    th.appendChild(document.createTextNode("基本操作"));


                    divtr = divtable.insertRow(-1);

                    //divtd = divtr.insertCell(-1);//
                    // var tda = document.createElement("a");
                    // tda.href = "http://www.85zu.com";
                    // tda.target = "_blank";
                    // tda.appendChild(document.createTextNode("挂账"));

                    // divtd.appendChild(tda);

                    // divtd = divtr.insertCell(-1);//
                    // var tda = document.createElement("a");
                    // tda.href = "http://www.85zu.com";
                    //  tda.target = "_blank";
                    // tda.appendChild(document.createTextNode("质量访问"));

                    // divtd.appendChild(tda);

                    //divtd = divtr.insertCell(-1);//
                    // var tda = document.createElement("a");
                    // tda.href = "http://www.85zu.com";
                    // tda.target = "_blank";
                    // tda.appendChild(document.createTextNode("确认件"));

                    //divtd.appendChild(tda);

                    divtd = divtr.insertCell(-1);//
                    divtd.className = 'last';

                    if('{$menurole.e_m}'=='Y'){
                        var tda = document.createElement("a");
                        tda.href =  "{:U($menuinfo['model'].'/add')}&id="+id+"&parentid="+parentid+"&menuid="+{$menuinfo['id']}+"&t=m&param="+encodeURIComponent(JSON2.stringify(param));

                        tda.appendChild(document.createTextNode("{:C('edit')}"));
                        divtd.appendChild(tda);
                    }else{
                        divtd.appendChild((document.createTextNode("")));
                    }


                    divtr = divtable.insertRow(-1);
                    //divtd = divtr.insertCell(-1);//
                    //var tda = document.createElement("a");
                    //tda.href = "http://www.85zu.com";
                    //tda.target = "_blank";
                    //tda.appendChild(document.createTextNode("业绩考核"));

                    // divtd.appendChild(tda);

                    // divtd = divtr.insertCell(-1);//
                    // var tda = document.createElement("a");
                    // tda.href = "http://www.85zu.com";
                    // tda.target = "_blank";
                    //  tda.appendChild(document.createTextNode("质量访问2"));

                    //  divtd.appendChild(tda);

                    //  divtd = divtr.insertCell(-1);//
                    //   if('{$menurole.a_m}'=='Y'){
                    //      var tda = document.createElement("a");
                    //      tda.href =  "{:U('Dwtfzhmsg/personadd')}&id="+id+"&menuid="+{$menuinfo['id']}+"&t=a&param="+encodeURIComponent(JSON2.stringify(param));
                    //      tda.appendChild(document.createTextNode("银行帐号列表 ("+this.zhcount +")"));
                    //      divtd.appendChild(tda);
                    //  }else{
                    //     divtd.appendChild((document.createTextNode("")));
                    // }


                    divtd = divtr.insertCell(-1);//
                    divtd.className = 'last';

                    if('{$menurole.a_m}'=='Y'){
                        var tda = document.createElement("a");
                        tda.href = "{:U('Dwtfperson/personadd')}&id="+id+"&menuid="+{$menuinfo['id']}+"&t=a&param="+encodeURIComponent(JSON2.stringify(param));
                        tda.appendChild(document.createTextNode("联系人列表 ("+this.subcount +")"));
                        divtd.appendChild(tda);
                    }else{
                        divtd.appendChild((document.createTextNode("")));
                    }

                    divtr = divtable.insertRow(-1);

                    divtd = divtr.insertCell(-1);//
                    if('{$menurole.a_m}'=='Y'){
                        var tda = document.createElement("a");
                        tda.href =  "{:U('Dwtfzhmsg/personadd')}&id="+id+"&menuid="+{$menuinfo['id']}+"&t=a&param="+encodeURIComponent(JSON2.stringify(param));
                        tda.appendChild(document.createTextNode("银行帐号列表 ("+this.zhcount +")"));
                        divtd.appendChild(tda);
                    }else{
                        divtd.appendChild((document.createTextNode("")));
                    }

                    div.appendChild(divtable);
                    li.appendChild(div);



                    var div1 = document.createElement("div");
                    div1.className = "angle sl-angle";
                    li.appendChild(div1);

                    ul.appendChild(li);

                    td.appendChild(ul);



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
<script language="javascript" defer>
    new PCAS("province","city","area","{$obj:q_province}","{$obj:q_city}","{$obj:q_area}");
</script>