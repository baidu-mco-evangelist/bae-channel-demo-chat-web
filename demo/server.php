<?php

require 'Channel.class.php';

if($_SERVER['REQUEST_METHOD'] === "POST" ){
  $method = $_POST['method'];
}else{
  $method = $_GET['method'];
}

$access_token = '3.e8613ed65eea2e3c240750d6f538b773.2592000.1356512862.1529687519-373207'; 

$dbname = 'fhIHXTPmiALVAeKgwmLr';

$host = getenv('HTTP_BAE_ENV_ADDR_SQL_IP');
$port = getenv('HTTP_BAE_ENV_ADDR_SQL_PORT');
$user = getenv('HTTP_BAE_ENV_AK');
$pwd = getenv('HTTP_BAE_ENV_SK');


$link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
if(!$link) {
	die("Connect Server Failed");
}
/*连接成功后立即调用mysql_select_db()选中需要连接的数据库*/
if(!mysql_select_db($dbname,$link)) {
	die("Select Database Failed: " . mysql_error($link));
}

mysql_query("set names utf8",$link);

if($method === 'join'){
	
  	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];
	$channel_id = $_POST['channel_id'];
  
    $chid =  mysql_query("select count(*) as count_row from chatuser where channelid='$channel_id'",$link);
  	
  	$info= mysql_fetch_array($chid);
	 
    if($info[count_row] == 0){
    
         mysql_query("insert into chatuser(userid,username,channelid) values('$user_id','$user_name','$channel_id')",$link);
         echo "refreshList";     	
    }
 	
}

if($method === 'getHistory'){
  
  	$user_id = $_POST['user_id'];
	$channel_id = $_POST['channel_id'];	
    //push 历史记录top 10
    $ret =  mysql_query("select msgid,messages from chatMessages ORDER BY msgid DESC LIMIT 0, 10",$link); 
    
  	$messagesList = array();
    $keyList = array();
  
  	$i = 0;
  	while($row = mysql_fetch_assoc($ret) ){
		$messagesList[$i] = $row['messages'];
      	$keyList[$i] = $row['msgid'];
        $i++;
	} 
  
    $messagesList = array_reverse($messagesList);
    $keyList = array_reverse($keyList); 
  
    $channel = new Channel ($access_token) ;
  	$ret = $channel->pushMessage($user_id, $channel_id, $messagesList, $keyList) ;
  
  	if ( false === $ret )
  	{
   	 	echo ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!\n' ) ;
    	echo ( 'ERROR NUMBER: ' . $channel->errno ( ) . '\n') ;
    	echo ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) . '\n' ) ;
    	echo ( 'REQUEST ID: ' . $channel->getRequestId ( ) . '\n');
  	}
  	else
  	{
    	echo ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!'. '\n' ) ;
    	echo ( 'result: ' . print_r ( $ret, true ) . '\n' ) ;
  	} 
  
}


if($method === 'getUserList'){
  
	$ret =  mysql_query("select distinct username from chatuser ",$link); 
  	$userlist = array();	
  	
  	$i = 0;
  	while($row =mysql_fetch_assoc($ret) ){
		$userlist[$i++] = $row['username'];
	}
  	echo json_encode(array('user'=> $userlist));
}

if($method === 'pushmesg'){
  	
  //    $access_token = $_POST["access_token"];
    $info = $_POST['message'];
    $userid = $_POST['userid'];
	$user_channel_id = $_POST['channel_id'];
  
  	mysql_query("insert into chatMessages(userid,messages) values('$userid','$info')",$link);
  
  	$user_temp = mysql_query("select id,userid,channelid from chatuser ",$link);
  
  	while($row = mysql_fetch_assoc($user_temp) ){
      	$user_id = $row['userid'];
      //        echo $user_id ;
        $channel_id = $row['channelid'];
      //        echo $channel_id ;
      
      	$id = $row['id'];
		
		if($user_channel_id == $channel_id){
			continue;
		}
      
      	$messages = array($info);
          
    	$msgkeys = array($id);
             
		$channel = new Channel ($access_token) ;
		$ret = $channel->pushMessage($user_id, $channel_id, $messages, $msgkeys) ;
		if ( false === $ret )
		{
			echo ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!\n' ) ;
			echo ( 'ERROR NUMBER: ' . $channel->errno ( ) . '\n') ;
			echo ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) . '\n' ) ;
			echo ( 'REQUEST ID: ' . $channel->getRequestId ( ) . '\n');
		}
		else
		{
			echo ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!'. '\n' ) ;
			echo ( 'result: ' . print_r ( $ret, true ) . '\n' ) ;
		}
      	
	}
  	
}

mysql_close($link);
?>