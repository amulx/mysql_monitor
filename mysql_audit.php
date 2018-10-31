<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equive="content-type" content="text/html" charset="UTF-8">
    <title>MySQL执行语句监控</title>
    <link rel="stylesheet" href="mysql.css">
</head>
<body>
<div class="bigtitle">MySQL执行语句监测</div>
<div class="smallcontent">
    <table class="tablecond">
        <input type="hidden" name="init_time">
        <tr class="tdc">
            <td class="tdc">主机:<input type="text" name="host"></td>
            <td class="tdc">用户:<input type="text" name="user"></td>
            <td class="tdc">密码:<input type="text" name="pwd"></td>
            <td rowspan="2"><a href="#" class="prolink" id="init">初始化</a></td>
            <td rowspan="2"><a href="#" class="prolink" id="pull">获取</a></td>
            <td rowspan="2"><a href="#" class="prolink" id="restore">还原</a></td>
        </tr>

    </table>
</div>
<div class="smallcontent">
    <table id="amutable">
        <tr>
            <td class="tdh" style="width: 250px;">时间</td>
            <td class="tdh" style="width: 900px;">语句</td>
        </tr>                      
    </table>
</div>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    $("#init").on("click", function(){
        var host = $('input[name=host]').val();
        var user = $('input[name=user]').val();
        var pwd = $('input[name=pwd]').val();
        if (host == "" || user == "" || pwd == "") {
            alert('主机或用户或密码不能为空');return false;
        }
        $.ajax({
            type: "POST",
            url: "init.php",
            data: "action=init&host="+host+"&user="+user+"&pwd="+pwd,
            success: function(msg){
                var ret = JSON.parse(msg);
                if (ret.code == '200') {
                    $('input[name=init_time]').val(JSON.parse(msg).data.cul_time)
                    alert( '初始化成功');
                } else {
                    alert(ret.msg);
                }
            }
        });
    });

    $("#pull").on("click", function(){
        var host = $('input[name=host]').val();
        var user = $('input[name=user]').val();
        var pwd = $('input[name=pwd]').val();        
        var init_time = $('input[name=init_time]').val();

        $.ajax({
            type: "POST",
            url: "init.php",
            data: "action=pull&init_time="+init_time+"&host="+host+"&user="+user+"&pwd="+pwd,
            success: function(msg){
                var ret = JSON.parse(msg);
                if (ret.code == '200') {
                    var html_str = '';
                    $.each(ret.data,function(i,item){
                        if (i % 2 == 1) {
                            html_str += '<tr><td class="tda td1">'+item.event_time+'</td><td class="tda td2">'+item.argument+'</td></tr>';
                        } else {
                            html_str += '<tr><td class="tdb td1">'+item.event_time+'</td><td class="tdb td2">'+item.argument+'</td></tr>';
                        }
                    　　console.log(i, item);
                    });
                    $("#amutable tbody").append(html_str);
                } else {
                    alert(ret.msg);
                }
            }
        });
    });

    $("#restore").on("click", function(){
        var host = $('input[name=host]').val();
        var user = $('input[name=user]').val();
        var pwd = $('input[name=pwd]').val();

        $.ajax({
            type: "POST",
            url: "init.php",
            data: "action=restore&host="+host+"&user="+user+"&pwd="+pwd,
            success: function(msg){
                var ret = JSON.parse(msg);
                if (ret.code == '200') {
                    alert(ret.msg);
                } else {
                    alert(ret.msg);
                }
            }
        });
    });    
</script>
</body>
</html>