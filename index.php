<?php

require 'Baidu.php';


$baidu = new Baidu('A364CFOoZtNnrsRlusRbHK5r', '6GP1XYB9d8fqY8HY0uGCONiZljvmQfGA',
	                new BaiduCookieStore('A364CFOoZtNnrsRlusRbHK5r'));
$baidu->useHttps();

// Get User ID
$user = $baidu->getLoggedInUser();


// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $baidu->getLogoutUrl(array('next' => 'http://channelchat.duapp.com/logout_callback2.php'));
  
} else {
  $loginUrl = $baidu->getLoginUrl(array('response_type' => 'code',
  										'redirect_uri' => 'http://channelchat.duapp.com/login_callback2.php'));
}

?>
<!doctype html>
<html>
  <head>
  	<meta http-equiv="content-type" content="text/html;charset=utf-8">
    <title>BAE-Channel-Chat</title>
    <link rel="stylesheet" type="text/css" href="LightFace.css"> 
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
	
	<script type="text/javascript" src="https://channel.api.duapp.com/jssdk/channel.min.js">
    </script>
    
	<script type="text/javascript" src="/demo/jquery-1.7.1.min.js">
    </script>
	
    <script type="text/javascript">
		var access_token = '<?php  $access_token = $baidu->getAccessToken(); echo $access_token; ?>';//请填入从开放平台获取的access_token
 		var channel_id = null;
		var channel_token = null;
		var user_id = null;
		Channel.token({
			'access_token': access_token
		}, function(res){
				channel_id = res.response_params.channel_id;
				channel_token = res.response_params.channel_token;
				user_id = res.response_params.user_id;
				joinChannel(channel_id, user_id,channel_token, access_token);
        	}
		);
		
		function joinChannel(channel_id, user_id, channel_token, access_token) {
			var postData = {
				'channel_id': channel_id,
				'method': 'join',
				'user_id': user_id,
                'user_name': "<?php echo $user['uname'] ?>",
                'access_token': access_token
			};
			$.ajax({
				url : '/demo/server.php', 
				data : postData,
				type : 'POST', 
				dataType : 'json',
				cache : false
				}).done(function(data) {
              
				})
				.fail(function(data, txt) {
                  //					alert("Internal Server Error" + data);
				})
				.always(function(data) {
					createChannel(channel_id, channel_token, access_token);
                  	list();
                  	getHistory()
				});
		}
		
		function createChannel(channel_id, channel_token, access_token) {
			Channel.on('open', function(e){							
              //				alert("access_token:"+access_token+"\n"+"id:"+user_id+"\n");
            });
			Channel.on('message', function(e){
				var message = e.data;
				var messages = message.response_params.messages;
				for (var i=0; i<messages.length; ++i){
                  //                    alert(messages[i].data);
                    document.getElementById('showMessage').value += messages[i].data+'\n';
              		document.getElementById('showMessage').scrollTop = document.getElementById('showMessage').scrollHeight;                  
				}

			});

			Channel.on("error", function(e){
				var err = e.data;
              //				alert("error:" + err);
        	});
		
			Channel.on("close", function(e){
				alert("connection closed");
	    	});
		
			Channel.create({
				'access_token': access_token,
				'channel_id': channel_id,
				'channel_token': channel_token
	    	}); 
        }
	</script>
    
    <script>
      function sendMessage(){
        
        message = "<?php echo $user['uname']  ?> "+ ": " + document.getElementById('putMessage').value;
        
        var putData = {
          		'userid': user_id,
                'message': message,
          		'method': 'pushmesg',
                'access_token': access_token
			};
			$.ajax({
				url : '/demo/server.php', 
				data : putData,
				type : 'POST', 
				dataType : 'json',
				cache : false
				}).done(function(data) {

				})
				.fail(function(data, txt) {

				})
				.always(function(data) {
					document.getElementById('putMessage').value = "";
				});       
      } 
    </script>
	
	<script>
      function list(){
        	 var getData = {
          		'method': 'getList',
                'access_token': access_token
			};
			$.ajax({
				url : '/demo/server.php', 
				data : getData,
				type : 'get', 
				dataType : 'json',
				cache : false
				}).done(function(data) {

				})
				.fail(function(data, txt) {

				})
				.always(function(data) {
                  	userList = data.user;
                  	for( i=0; i< userList.length; i++){
                    	document.getElementById('showUser').value += userList[i]+'\n';
                    }
				});
      }
    </script>
    
   	<script>
      function getHistory(){
        	 var getData = {
				'channel_id': channel_id,
				'method': 'getHistory',
				'user_id': user_id,
			};
			$.ajax({
				url : '/demo/server.php', 
				data : getData,
				type : 'post', 
				dataType : 'json',
				cache : false
				}).done(function(data) {

				})
				.fail(function(data, txt) {

				})
				.always(function(data) {

				});
      }
    </script>
    
  </head>
  <body>
  <h1><center>
  </center>
  </h1>

    <?php if ($user): ?>
	
		<table width="1421" height="31" border="0">
  			<tr>
    			<td width="34">&nbsp;</td>
				<td width="219">user:<?php echo($user['uname']); ?></td>
				<td width="969">&nbsp;</td>
				<td width="181"><a id="logoutfrombaidu" href="<?php echo $logoutUrl; ?>">Logout</a></td>
			</tr>
		</table>  
    <?php else: ?>	
		<table width="1421" height="31" border="0">
  			<tr>
    			<td width="34">&nbsp;</td>
				<td width="219">&nbsp;</td>
				<td width="969">&nbsp;</td>
				<td width="181"><a id="loginwithbaidu" href="#">Login</a></td>
			</tr>
		</table> 
    <?php endif ?>

    <?php if ($user): ?>
	  <table width="1421" border="0">
	  		<tr>
    			<td width="150"><textarea name="textarea" cols="23" rows="45" id = 'showUser' readonly="readonly"></textarea></td>
				<td>   				
						  <textarea cols="160" rows="35" readonly="readonly" id="showMessage"></textarea>
						  <textarea cols="160" rows="6" id="putMessage" onkeydown="javascript:if(event.keyCode==13)if(document.getElementById('putMessage').value) sendMessage();else alert('message is null');""></textarea>
				          <p>
						  <input name="start" value="send" type="button" style="width:60px;height:30px;" onClick="sendMessage();">	
						  </p>
	            </td>
			</tr>
	  
	  </table> 
    

      <?php endif ?>
      <div id="logindialog"></div>
    
    <script type="text/javascript" src="mootools-1.3.js"></script> 
	<script type="text/javascript" src="LightFace.js"></script> 
	<script type="text/javascript" src="LightFace.IFrame.js"></script> 
    
    <script>
	
    <?php if (!$user): ?>
    document.id('loginwithbaidu').addEvent('click',function() {
    	new LightFace.IFrame({height:320, width:560, url: '<?php echo $loginUrl;?>'}).open();
    });
    new LightFace.IFrame({height:320, width:560, url: '<?php echo $loginUrl;?>'}).open();
    
    <?php else: ?>
    document.id('logoutfrombaidu').addEvent('click', function() {
		document.getElementById('logout_form').submit();
		return false;
    });
    <?php endif ?>
    </script>
  <script src="http://app.baidu.com/static/appstore/monitor.st"></script>
  <script>
  baidu.app.autoHeight();
  baidu.app.setHeight(400);
  </script>
    
  </body>
</html>