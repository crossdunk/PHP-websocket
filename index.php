<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8' />
<style type="text/css">
<!--
.chat_wrapper {
	width: 800px;
	border-radius:15px;
	margin-right: auto;
	margin-left: auto;
	background: #96C8F1;
	border: 1px solid #999999;
	padding: 10px;
	font: 12px 'lucida grande',tahoma,verdana,arial,sans-serif;
}
.chat_wrapper .message_box {
	background: #FFFFFF;
	height: 300px;
	overflow: auto;
	padding: 20px;
	border: 1px solid #999999;
}
.chat_wrapper .panel input{
	padding: 2px 2px 2px 5px;
}
.system_msg{color: #BDBDBD;}
.user_name{
	font-weight:bold;
	font-size: 16px;
}
.user_message{
	color: #88B6E0;
	font-size: 16px;
}
-->
</style>
</head>
<body>	
<div class="chat_wrapper">
<div class="message_box" id="message_box"></div>
<div class='chose_color'>
暱稱顏色<select id='chose_color' class='chose_color_code'>
	<option value='000000'>黑色</option>
	<option value='F4244E'>紅色</option>
	<option value='FA7C58'>橙色</option>
	<option value='F773ED'>粉色</option>
	<option value='41A516'>綠色</option>
	<option value='28A3D0'>藍色</option>
	<option value='886DF3'>紫色</option>
</select>
字的顏色<select id='chose_color_text'  class='chose_color_code'>
	<option value='000000'>黑色</option>
	<option value='F4244E'>紅色</option>
	<option value='FA7C58'>橙色</option>
	<option value='F773ED'>粉色</option>
	<option value='41A516'>綠色</option>
	<option value='28A3D0'>藍色</option>
	<option value='886DF3'>紫色</option>
</select>
</div>
<div class="panel">
<input type="text" name="message" id="message" placeholder="Message" maxlength="80" style="width:90%" />
</div>
</div>

</body>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

<script language="javascript" type="text/javascript">  
	//輸入nickname
	nickname = '';
	
	var connect_server = function(){
		if(nickname){
			var wsUri = "ws://10.1.114.71:9000/server.php"; 	
			websocket = new WebSocket(wsUri); 
			websocket.onopen = function(ev) { // connection is open 
				$('#message_box').append("<div class=\"system_msg\">連接成功!</div>");
			}
		}else{
			$('#message_box').append("<div class=\"system_msg\">請重整並請輸入暱稱!</div>");
			return false;
		}
    }

	var input_nickname = function(){
      nickname = prompt(" 输入使用的暱稱 ");
      if(!nickname){
        input_nickname();
      }else{
        connect_server();
      }
    }
    input_nickname();
    //create a new WebSocket object.
    

	$('#message').keypress(function(e) {
      if ( e.keyCode == 13 && this.value ) {
       var mymessage = $('#message').val(); //get message text
		var color = $('#chose_color').val(); //nickname color
		var text_color = $('#chose_color_text').val(); //message color
		if(nickname == ""){ //empty name?
			alert("請輸入暱稱");
			return;
		}
		if(mymessage == ""){ //emtpy message?
			alert("你沒有輸入訊息");
			return;
		}
		
		//prepare json data
		var msg = {
		message: mymessage,
		name: nickname,
		color : color,
		text_color:text_color
		};
		//convert and send data to server
		websocket.send(JSON.stringify(msg));
      }
    });
	
	// 從server收到訊息
	scrolltop = 0; //控制滾輪
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type; //message type
		var umsg = msg.message; //message text
		var uname = msg.name; //user name
		var ucolor = msg.color; //nickname color
		var utext_color = msg.text_color
		if(type == 'usermsg') 
		{
			var NowDate=new Date();
		　 var h=NowDate.getHours();
		　 var m=NowDate.getMinutes();
		　 var s=NowDate.getSeconds();　
		　 var time_now = h+':'+m+':'+s;
			$('#message_box').append("<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+uname+"</span> : <span class=\"user_message\" style=\"color:#"+utext_color+"\">"+umsg+" - </span>"+ time_now+"</div>");
			scrolltop = scrolltop + 10; //維持視窗在最後一句
			$('#message_box').scrollTop(scrolltop);
		}
		if(type == 'system')
		{
			$('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
			scrolltop = scrolltop + 10; //維持視窗在最後一句
			$('#message_box').scrollTop(scrolltop);
		}
		
		$('#message').val(''); //reset text
	};
	
	websocket.onerror	= function(ev){$('#message_box').append("<div class=\"system_error\">錯誤 - "+ev.data+"</div>");}; 
	websocket.onclose 	= function(ev){$('#message_box').append("<div class=\"system_msg\">連結已關閉，或未開啟伺服器</div>");}; 

	$('.chose_color_code option').each(function(){
		var color_code = $(this).val();
		$(this).css({background:'#'+color_code});
	});

	$('#chose_color_text').bind('change',function(){
		var color_code = $(this).val();
		$('#message').css({color:'#'+color_code});
	});
</script>
</html>