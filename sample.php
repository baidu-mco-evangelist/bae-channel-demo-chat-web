<?php
require_once ( "Channel.class.php" ) ;

//$access_token = '1.8a9a07e1c8d864ea79d32f932c08de5b.86400.1334912437.2819575091-101973';

function error_output ( $str ) 
{
	echo "\033[1;40;31m" . $str ."\033[0m" . "\n";
}

function right_output ( $str ) 
{
    echo "\033[1;40;32m" . $str ."\033[0m" . "\n";
}


function test_queryBindList ( $userId, $access_token ) 
{
	global $access_token;
	$channel = new Channel ( $access_token ) ;
	//$optional [ Channel::CHANNEL_ID ] = 2484515682371722163; 
	$ret = $channel->queryBindList ( $userId, $optional ) ;
	if ( false === $ret ) 
	{
		error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
		error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
		error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
		error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
	}
	else
	{
		right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
		right_output ( 'result: ' . print_r ( $ret, true ) ) ;
	}	
}


function test_verifyBind ( $userId, $access_token  )
{
    global $access_token;
    $channel = new Channel ( $access_token ) ;
    //$optional [ Channel::CHANNEL_ID ] = 2484515682371722163;
    $ret = $channel->verifyBind ( $userId, $optional ) ;
    if ( false === $ret )
    {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    }
    else
    {
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }
}

function test_pushMessage ( $userId, $channel_id, $messages, $msgIds , $access_token )
{
    global $access_token;
    $channel = new Channel ( $access_token ) ;
    $ret = $channel->pushMessage ( $userId, $channel_id, $messages, $msgIds, $optional ) ;
    if ( false === $ret )
    {
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    }
    else
    {
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }
}


function test_pushMessageToUser ( $userId, $messages, $msgKeys, $access_token  )
{
    global $access_token;
    $channel = new Channel ( $access_token ) ;
    //$optional [ Channel::CHANNEL_ID ] = 4152049051604943232;
    $ret = $channel->pushMessageToUser ( $userId, $messages, $msgKeys, $optional ) ;
    if ( false === $ret )
    {
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    }
    else
    {
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }
}

function test_messageCount ( $userId, $access_token   )
{
    global $access_token;
    $channel = new Channel ( $access_token ) ;
    //$optional [ Channel::CHANNEL_ID ] = 4152049051604943232;
    $ret = $channel->messageCount ( $userId, $optional ) ;
    if ( false === $ret )
    {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    }
    else
    {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }
}

function test_fetchMessage ( $userId , $access_token  )
{
    global $access_token;
    $channel = new Channel ($access_token ) ;
    //$optional [ Channel::CHANNEL_ID ] = 4152049051604943232;
    $ret = $channel->fetchMessage ( $userId, $optional ) ;
    if ( false === $ret )
    {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    }
    else
    {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }
}

function test_deleteMessage ( $userId, $msgIds, $access_token  )
{
    global $access_token;
    $channel = new Channel ($access_token ) ;
    //$optional [ Channel::CHANNEL_ID ] = 4152049051604943232;
    $ret = $channel->deleteMessage ( $userId, $msgIds, $optional ) ;
    if ( false === $ret )
    {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    }
    else
    {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }
}


function test_createGroup($group_name, $info, $access_token )
{
    global $access_token;
    $channel = new Channel($access_token);
    $optional[Channel:: GROUP_INFO] = $info;
    $ret = $channel->createGroup($group_name, $optional);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
        return false;
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
        return $ret['response_params']['gid'];
    }
}

function test_queryGroup($gid)
{
    global $access_token;
    $channel = new Channel($access_token);
    $ret = $channel->queryGroup($gid);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }

}


function test_destroyGroup($gid)
{
    global $access_token;
    $channel = new Channel($access_token);
    $ret = $channel->destroyGroup($gid);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }

}


function test_queryUserGroup($user_id)
{
    global $access_token;
    $channel = new Channel($access_token);
    $ret = $channel->queryUserGroup($user_id);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }

}


function test_pushGroupMsg($messages, $mesg_keys, $device_type, $gid)
{
    global $access_token;
    $channel = new Channel($access_token);
    $optional[Channel::DEVICE_TYPE] = $device_type;
    $optional[Channel::GROUP_ID] = $gid;
    $ret = $channel->pushGroupMsg($messages, $msg_keys, $optional);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }

}


function test_fetchGroupMsg($gid)
{
    global $access_token;
    $channel = new Channel($access_token);
    $optional[Channel::GROUP_ID] = $gid;
    $ret = $channel->fetchGroupMsg($optional);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
        return false;
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
        return $ret['response_params']['messages'][0]['msg_id'];
    }

}


function test_fetchGroupMsgcount($gid)
{
    global $access_token;
    $channel = new Channel($access_token);
    $optional[Channel::GROUP_ID] = $gid;
    $ret = $channel->fetchGroupMsgcount($optional);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
        return false;
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
        return $ret['response_params']['messages'][0]['msg_id'];
    }

}


function test_deleteGroupMsg($msg_ids, $gid)
{
    global $access_token;
    $channel = new Channel($access_token);
    $optional[Channel::GROUP_ID] = $gid; 
    $ret = $channel->deleteGroupMsg($msg_ids, $optional);
    if (false === $ret) {   
        error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
        error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
        error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
        error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    } else {   
        right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
        right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    }

}