<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8' />
<style type="text/css">
<!--
.chat_wrapper {
	width: 500px;
	margin-right: auto;
	margin-left: auto;
	background: #CCCCCC;
	border: 1px solid #999999;
	padding: 10px;
	font: 12px 'lucida grande',tahoma,verdana,arial,sans-serif;
}
.chat_wrapper .message_box {
	background: #FFFFFF;
	height: 150px;
	overflow: auto;
	padding: 10px;
	border: 1px solid #999999;
}
.chat_wrapper .panel input{
	padding: 2px 2px 2px 5px;
}
.system_msg{color: #BDBDBD;font-style: italic;}
.user_name{font-weight:bold;}
.user_message{color: #88B6E0;}
-->
</style>
</head>
<body>	
<?php 
$colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
$user_colour = array_rand($colours);
?>


<div class="chat_wrapper">
<div class="message_box" id="message_box"></div>
<div class='chose_color'>
暱稱顏色<select id='chose_color'>
	<option value='F4244E'>紅色</option>
	<option value='FA7C58'>橙色</option>
	<option value='F773ED'>粉色</option>
	<option value='41A516'>綠色</option>
	<option value='28A3D0'>藍色</option>
	<option value='886DF3'>紫色</option>
</select>
字的顏色<select id='chose_color_text'>
	<option value='F4244E'>紅色</option>
	<option value='FA7C58'>橙色</option>
	<option value='F773ED'>粉色</option>
	<option value='41A516'>綠色</option>
	<option value='28A3D0'>藍色</option>
	<option value='886DF3'>紫色</option>
</select>
</div>
<div class="panel">
<input type="text" name="message" id="message" placeholder="Message" maxlength="80" style="width:80%" />
<button id="send-btn">Send</button>
</div>
</div>

</body>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

<script language="javascript" type="text/javascript">  
	//輸入nickname
	nickname = '';
	
	var connect_server = function(){
       var wsUri = "ws://10.1.114.71:9000/server.php"; 	
		websocket = new WebSocket(wsUri); 
		websocket.onopen = function(ev) { // connection is open 
			$('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
		}
    }

	var input_nickname = function(){
      nickname = prompt(" 输入使用的暱稱 ");
      if(nickname==''){
        input_nickname();
      }else{
        connect_server();
      }
    }
    input_nickname();
    //create a new WebSocket object.
    

	
	

	$('#send-btn').click(function(){ //use clicks message send button	
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
	});
	
	//#### Message received from server? 從server收到訊息
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type; //message type
		var umsg = msg.message; //message text
		var uname = msg.name; //user name
		var ucolor = msg.color; //nickname color
		var utext_color = msg.text_color
		if(type == 'usermsg') 
		{
			$('#message_box').append("<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+uname+"</span> : <span class=\"user_message\" style=\"color:#"+utext_color+"\">"+umsg+"</span></div>");
		}
		if(type == 'system')
		{
			$('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
		}
		
		$('#message').val(''); //reset text
	};
	
	websocket.onerror	= function(ev){$('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
	websocket.onclose 	= function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");}; 
</script>
</html>