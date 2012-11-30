var access_token;
var user_name;
var channel_id;
var channel_token;
var user_id;

baidu.require('connect', function(connect){
	connect.init( 'IZjFcoPz5Sw0c5uIXwij4rWw',{
		status:true
	});
	
	document.getElementById('login').onclick= function(){
		connect.login(function(info){
			access_token = info.session.access_token;//获取access_token
			
			document.getElementById('login').style.display = "none";
			document.getElementById('logout').style.display = "block";
			document.getElementById('input_message').removeAttribute("readonly");
			document.getElementById('send_message').disabled = false;
			
			connect.api({
				url: 'passport/users/getLoggedInUser',
				onsuccess: function(info){
					user_name = info.uname;
					startChannel();
				},
				onnotlogin: function(){
					
				},
				params:{
				  "access_token": access_token	
				}
			});
					
		});
	}; 

	document.getElementById('logout').onclick= function(){
		connect.logout(function(info){
			document.getElementById('login').style.display = "block";
			document.getElementById('logout').style.display = "none";
			document.getElementById('input_message').readOnly = "true";
			document.getElementById('send_message').disabled = true;	
			document.getElementById('show_messages').value ="";		
		});			
	};

});

function startChannel(){

	Channel.token({
		'access_token': access_token
	}, function(res){
			channel_id = res.response_params.channel_id;
			channel_token = res.response_params.channel_token;
			user_id = res.response_params.user_id;
			joinChannel(channel_id, user_id,channel_token, access_token);
		}
	);	
	
}

function joinChannel(channel_id, user_id, channel_token, access_token) {
		var postData = {
			'channel_id': channel_id,
			'method': 'join',
			'user_id': user_id,
			'user_name': user_name,
			'access_token': access_token
		};
		$.ajax({
			url : 'demo/server.php', 
			data : postData,
			type : 'POST', 
			dataType : 'json',
			cache : false
			}).done(function(data) {
										
		  		//createChannel(channel_id, channel_token, access_token);
				/*list();
				getHistory()*/
			})
			.fail(function(data, txt) {
			  	/*alert("Internal Server Error :" + txt);*/
			})
			.always(function(data) {
				createChannel(channel_id, channel_token, access_token);
			});
	}
	
function createChannel(channel_id, channel_token, access_token) {
	Channel.on('open', function(e){							
//		  	alert("access_token:"+access_token+"\n"+"id:"+user_id+"\n");
	});
	Channel.on('message', function(e){
		var message = e.data;
		var messages = message.response_params.messages;
		for (var i=0; i<messages.length; ++i){
		  //                    alert(messages[i].data);
				document.getElementById('show_messages').value += messages[i].data+'\n';
				document.getElementById('show_messages').scrollTop = document.getElementById('show_messages').scrollHeight;                  
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

//发送信息
function sendMessage(){
	
	message = user_name + ": " + document.getElementById('input_message').value;
	document.getElementById('input_message').value = "";
	
	document.getElementById('show_messages').value += message +'\n'
	
	var putData = {
			'userid': user_id,
			'message': message,
			'method': 'pushmesg',
			'access_token': access_token,
			'channel_id': channel_id
		};
		$.ajax({
			url : 'demo/server.php', 
			data : putData,
			type : 'POST', 
			dataType : 'json',
			cache : false
			}).done(function(data) {

			})
			.fail(function(data, txt) {

			})
			.always(function(data) {
				
			});       
} 

//获得用户列表
function getUserList(){
	var getData = {
		'method': 'getUserList',
		'access_token': access_token
	};
	$.ajax({
		url : 'demo/server.php', 
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

//获得历史信息（最新10条）
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

/*function textareaKeydown(){
	if(event.ctrlKey && event.keyCode==13){		
		if(document.getElementById('putMessage').value){
			sendMessage();
		}else{
			alert('message is null');
		}
	}
 	if(event.ctrlKey && event.keyCode==13){	
		document.getElementById('putMessage').value += '\n'; 
	} 
}*/

