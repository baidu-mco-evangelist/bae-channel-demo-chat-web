<?php
/**
 * 百度云消息通道服务 PHP SDK
 * 
 * 本文件提供百度云消息通道服务的PHP版本SDK
 * 
 * @author 百度移动.云事业部
 * @copyright Copyright (c) 2012-2020 百度在线网络技术(北京)有限公司
 * @version 2.0.0
 * @package
 */
if ( ! defined ( 'API_ROOT_PATH' ) ) 
{
	define ( 'API_ROOT_PATH', dirname( __FILE__));
}
require_once ( API_ROOT_PATH . '/lib/RequestCore.class.php' );
require_once ( API_ROOT_PATH . '/lib/ChannelException.class.php' );
require_once ( API_ROOT_PATH . '/lib/BaeBase.class.php' );

/**
 * 
 * Channel
 * 
 * Channel类提供百度云消息通道服务的PHP版本SDK，用户首先实例化这个类，设置自己的access_token，即可使用百度云消息通道服务
 * 
 * @author 百度云消息通道服务@百度云架构部
 * 
 * @version 1.0.0.0
 */
class Channel extends BaeBase
{
	/**
	 * 可选参数的KEY
	 * 
	 * 用户关注：是
	 * 在调用Channel类的SDK方法时，根据用户的个性化需要，可能需要传入可选参数，而可选参数需要放在关联数组$optional中传入，
	 * 这里定义了$optional数组可用的KEY
	 */
	
	/**
	 * 发起请求时的时间戳
	 * 
	 * @var int TIMESTAMP
	 */
	const TIMESTAMP = 'timestamp';
	/**
	 * 请求过期的时间
	 * 
	 * 如果不填写，默认为10分钟
	 * 
	 * @var int EXPIRES
	 */
	const EXPIRES = 'expires';
	/**
	 * API版本号
	 * 
	 * 用户一般不需要关注此项
	 * 
	 * @var int VERSION
	 */
	const VERSION = 'v';
	/**
	 * 消息通道ID号
	 * 
	 * @var int CHANNEL_ID
	 */
	const CHANNEL_ID = 'channel_id';
	/**
	 * 用户ID的类型
	 * 
	 * 0：百度用户标识对称加密串；1：百度用户标识明文
	 * 
	 * @var string USER_TYPE
	 */
	const USER_TYPE = 'user_type';
	/**
	 * 设备类型
	 * 
	 * 1：浏览器设备；2：PC设备；3：andorid设备
	 * 
	 * @var int DEVICE_TYPE
	 */
	const DEVICE_TYPE = 'device_type';
	/**
	 * 第几页
	 * 
	 * 批量查询时，需要指定pageno，默认为第0页
	 * 
	 * @var int PAGENO
	 */
	const PAGENO = 'pageno';
	/**
	 * 每页多少条记录
	 * 
	 * 批量查询时，需要指定limit，默认为100条
	 * 
	 * @var int LIMIT
	 */
	const LIMIT = 'limit';
	/**
	 * 消息ID json字符串
	 * 
	 * @var string MSG_IDS
	 */
	const MSG_IDS = 'msg_ids';
	const MSG_KEYS = 'msg_keys';
	const IOS_MESSAGES = 'ios_messages';
	const WP_MESSAGES = 'wp_messages';
	/**
	 * 消息类型
	 * 
	 * 扩展类型字段，0：默认类型
	 * 
	 * @var int MESSAGE_TYPE
	 */
	const MESSAGE_TYPE = 'message_type';
	/**
	 * 消息超时时间
	 * 
	 * @var int MESSAGE_EXPIRES
	 */
	const MESSAGE_EXPIRES = 'message_expires';
    
    /**
     * 消息广播组名称
     * 
     * @var string GROUP_NAME
     */
    const GROUP_NAME = 'name';
    
    /**
     * 消息广播组描述
     * 
     * @var stirng GROUP_INFO
     */
    const GROUP_INFO = 'info';
    
    /**
     * 消息广播组id
     * 
     * @var int GROUP_ID
     */
    const GROUP_ID = 'gid';
    
    /**
     * 封禁时间
     * 
     * @var int BANNED_TIME
     */
    const BANNED_TIME = 'banned_time';
    
    /**
     * 回调域名
     * 
     * @var string CALLBACK_DOMAIN
     */
    const CALLBACK_DOMAIN = 'domain';
    
    /**
     * 回调uri
     * 
     * @var string CALLBACK_URI
     */
    const CALLBACK_URI = 'uri';

	/**
	 * Channel常量
	 * 
	 * 用户关注：否
	 */
	const APPID = 'appid';
	const ACCESS_TOKEN = 'access_token';
	const ACCESS_KEY = 'client_id';
	const SECRET_KEY = 'client_secret';
	const SIGN = 'sign';
	const METHOD = 'method';
	const HOST = 'host';
	const USER_ID = 'user_id';
	const MESSAGES = 'messages';
	const PRODUCT = 'channel';
	
	const DEFAULT_HOST = 'channel.api.duapp.com';
	const NAME = "name";
	const DESCRIPTION = "description";
	const CERT = "cert"; 
	const RELEASE_CERT = "release_cert";
	const DEV_CERT = "dev_cert";
	
	/**
	 * Channel私有变量
	 * 
	 * 用户关注：否
	 */
	protected $_accessToken = NULL;
	protected $_requestId = 0;
	protected $_curlOpts = array(
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 5
        );

	/**
	 * Channel 错误常量
	 * 
	 * 用户关注：否
	 */
	const CHANNEL_SDK_SYS = 1;
	const CHANNEL_SDK_INIT_FAIL = 2;
	const CHANNEL_SDK_PARAM = 3;
	const CHANNEL_SDK_HTTP_STATUS_ERROR_AND_RESULT_ERROR = 4;
	const CHANNEL_SDK_HTTP_STATUS_OK_BUT_RESULT_ERROR = 5;

	/**
	 * 错误常量与错误字符串的映射
	 * 
	 * 用户关注：否
	 */
	protected $_arrayErrorMap = array
		( 
		 '0' => 'php sdk error',
		 self::CHANNEL_SDK_SYS => 'php sdk error',
		 self::CHANNEL_SDK_INIT_FAIL => 'php sdk init error',
		 self::CHANNEL_SDK_PARAM => 'lack param',
		 self::CHANNEL_SDK_HTTP_STATUS_ERROR_AND_RESULT_ERROR => 'http status is error, and the body returned is not a json string',
		 self::CHANNEL_SDK_HTTP_STATUS_OK_BUT_RESULT_ERROR => 'http status is ok, but the body returned is not a json string',
		);
	
	public function setAccessToken ( $accessToken )
	{
		$this->_resetErrorStatus (  );
		try
		{
			if ( $this->_checkString ( $accessToken, 1, 256 ) )
			{
				$this->_accessToken = $accessToken;
			}
			else 
			{
				throw new ChannelException ( "invaid accessToken ( ${accessToken} ), which must be a 1 - 256 length string", self::CHANNEL_SDK_INIT_FAIL );
			}
		}
		catch ( Exception $ex )
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
		return true;
	}
	
	/**
	 * setCurlOpts
	 * 
	 * 用户关注：是
	 * 服务类方法， 设置HTTP交互的OPTION，同PHP curl库的所有opt参数
	 * 
	 * @access public
	 * @param array $arr_curlopt
	 * @return 成功：true，失败：false
	 * @throws BcmsException
	 * 
	 * @version 1.2.0
	 */
	public function setCurlOpts($arr_curlOpts)
	{
		$this->_resetErrorStatus();
		try {
			if (is_array($arr_curlOpts)) {
				$this->_curlOpts = array_merge($this->_curlOpts, $arr_curlOpts);
			}
			else  {
				throw new ChannelException( 'invalid param - arr_curlOpts is not an array ['
                        . print_r($arr_curlOpts, true) . ']',
                        self::CHANNEL_SDK_INIT_FAIL);
			}
		} catch (Exception $ex) {
			$this->_channelExceptionHandler( $ex );
			return false; 
		}
		return true;
	}

	/**
	 * getRequestId
	 * 
	 * 用户关注：是
	 * 服务类方法，获取上次调用的request_id，如果SDK本身错误，则直接返回0
	 * 
	 * @access public
	 * @return 上次调用服务器返回的request_id
	 * 
	 * @version 1.0.0.0
	 */
	public function getRequestId (  )
	{
		return $this->_requestId;
	}
	
	/**
	 * bindList
	 * 
	 * 用户关注：是
	 * 
	 * 供服务器端根据userId[、channelId]查询绑定信息
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::CHANNEL_ID、Channel::DEVICE_TYPE、Channel::PAGENO、Channel::LIMIT
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function queryBindList ( $userId, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'query_bindlist';
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * bindVerify
	 * 
	 * 用户关注：是
	 * 
	 * 校验userId[、channelId]是否已经绑定
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::CHANNEL_ID、Channel::DEVICE_TYPE
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function verifyBind ( $userId, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'verify_bind';
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * fetchMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId[、channelId]查询离线消息
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::CHANNEL_ID、Channel::PAGENO、Channel::LIMIT
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function fetchMessage ( $userId, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'fetch_msg';
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * messageCount
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId[、channelId]查询离线消息的个数
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::CHANNEL_ID
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function messageCount ( $userId, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'fetch_msgcount';
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * deleteMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId、msgIds[、channelId]删除离线消息
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $msgIds 要删除哪些消息,如果是数组格式，则会自动做json_encode;
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::CHANNEL_ID
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function deleteMessage ( $userId, $msgIds, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::MSG_IDS ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'delete_msg';
			if(is_array($arrArgs [ self::MSG_IDS ])) {
				$arrArgs [ self::MSG_IDS ] = json_encode($arrArgs [ self::MSG_IDS ]);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::MSG_IDS ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * pushMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId、msgIds[、channelId]推送离线消息
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $channelId 觕hannel的ID号
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode;如果是json格式给出，必须与$msgIds对应起来;
	 * @param string $msgKeys 发送的消息key，如果是数组格式，则会自动做json_encode;如果是json字符串格式给出，必须与$messages对应起来;
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::MESSAGE_TYPE、Channel::MESSAGE_EXPIRES
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function pushMessage ( $userId, $channelId, $messages, $msgKeys, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'pushmsg';
			if(is_array($arrArgs [ self::MESSAGES ])) {
				$arrArgs [ self::MESSAGES ] = json_encode($arrArgs [ self::MESSAGES ]);
			}
			if(is_array($arrArgs [ self::MSG_KEYS ])) {
				$arrArgs [ self::MSG_KEYS ] = json_encode($arrArgs [ self::MSG_KEYS ]);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}

	/**
	 * pushMessageToUser
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId推送离线消息
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode;如果是json格式给出，必须与$msgIds对应起来;
	 * @param string $msgKeys 发送的消息key，如果是数组格式，则会自动做json_encode;如果是json字符串格式给出，必须与$messages对应起来;
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::IOS_MESSAGES、Channel::WP_MESSAGES、Channel::DEVICE_TYPE、Channel::MESSAGE_TYPE、Channel::MESSAGE_EXPIRES
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function pushMessageToUser ( $userId, $messages, $msgKeys, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::MESSAGES, self::MSG_KEYS ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'pushmsg_to_user';
			if(is_array($arrArgs [ self::MESSAGES ])) {
				$arrArgs [ self::MESSAGES ] = json_encode($arrArgs [ self::MESSAGES ]);
			}
			if(is_array($arrArgs [ self::MSG_KEYS ])) {
				$arrArgs [ self::MSG_KEYS ] = json_encode($arrArgs [ self::MSG_KEYS ]);
			}
			if(isset($arrArgs [ self::IOS_MESSAGES ]) && is_array($arrArgs [ self::IOS_MESSAGES ])) {
				$msgs = array();
				foreach ( $arrArgs[self::IOS_MESSAGES] as $message ) {
					array_push($msgs, json_encode($message));
				}
				$arrArgs [ self::IOS_MESSAGES ] = json_encode($msgs);
			}
			if(isset($arrArgs [ self::WP_MESSAGES ]) && is_array($arrArgs [ self::WP_MESSAGES ])) {
				$msgs = array();
				foreach ( $arrArgs[self::WP_MESSAGES] as $message ) {
					array_push($msgs, json_encode($message));
				}
				$arrArgs [ self::WP_MESSAGES ] = json_encode($msgs);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::MESSAGES, self::MSG_KEYS ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
   
	/**
	 * pushIosMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId、channelId、messages 推送消息，仅支持ios设备
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $channelId 觕hannel的ID号
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode
	 * @param array $optional 可选参数，支持的可选参数包括 self::USER_TYPE
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */

	public function pushIosMessage($userId, $channelId, $messages, $optional = NULL) 
	{
		
		$this->_resetErrorStatus();
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'push_ios_msg';
			if(is_array($arrArgs [ self::MESSAGES ])) {
				$msgs = array();
				foreach ( $arrArgs[self::MESSAGES] as $message ) {
					array_push($msgs, json_encode($message));
				}
				$arrArgs [ self::MESSAGES ] = json_encode($msgs);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
		
	}
	
	/**
	 * pushWpMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId、channelId、messages 推送消息，仅支持windows phone设备
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $channelId channel的ID号
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode
	 * @param array $optional 可选参数，支持的可选参数包括 self::USER_TYPE
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function pushWpMessage($userId, $channelId, $messages, $optional = NULL) 
	{
		$this->_resetErrorStatus();
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'push_wp_msg';
			if(is_array($arrArgs [ self::MESSAGES ])) {
				$msgs = array();
				foreach ( $arrArgs[self::MESSAGES] as $message ) {
					array_push($msgs, json_encode($message));
				}
				$arrArgs [ self::MESSAGES ] = json_encode($msgs);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}	
 
    /**
     * createGroup: 创建消息广播组
     * 
     * 用户关注: 是
     *
     * @access public
     * @param string $groupName 广播组名称
     * @param array $optional 可选参数，支持的可选参数包括 self::GROUP_INFO
     * @return 成功: array; 失败: false
     * 
     * @version 1.0.0.0
     */
    public function createGroup($groupName, $optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::GROUP_NAME), $tmpArgs);
            $arrArgs[self::METHOD] = 'create_group';
            return $this->_commonProcess($arrArgs, array(self::GROUP_NAME));
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }
    
    /**
     * queryGroup: 查询广播组信息
     * 
     * 用户关注: 是
     *
     * @param int $groupId 广播组ID号
     * @param array $optional
     * @return 成功：PHP数组；失败：false
     */
    public function queryGroup($groupId, $optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::GROUP_ID), $tmpArgs);
            $arrArgs[self::METHOD] = 'query_group';
            return $this->_commonProcess($arrArgs, array(self::GROUP_ID));
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }
    
    /**
     * destroyGroup: 删除广播组
     * 
     * 用户关注: 是
     *
     * @param int $groupId 广播组ID号
     * @param array $optional
     * @return 成功：PHP数组；失败：false
     */
    public function destroyGroup($groupId, $optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::GROUP_ID), $tmpArgs);
            $arrArgs[self::METHOD] = 'destroy_group';
            return $this->_commonProcess($arrArgs, array(self::GROUP_ID));
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }
    
    /**
     * queryUserGroup: 查询用户相关的广播组
     * 
     * 用户关注: 是
     *
     * @param string $userId 用户ID号
     * @param array $optional
     * @return 成功：PHP数组；失败：false 
     */
    public function queryUserGroup($userId, $optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::USER_ID), $tmpArgs);
            $arrArgs[self::METHOD] = 'query_user_group';
            return $this->_commonProcess($arrArgs, array(self::USER_ID));
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }
    
    /**
     * pushGroupMsg: 向广播组推送消息
     * 
     * 用户关注: 是
     *
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode;如果是json格式给出，必须与$msgIds对应起来;
	 * @param string $msgKeys 发送的消息key，如果是数组格式，则会自动做json_encode;如果是json字符串格式给出，必须与$messages对应起来;
     * @param array $optional可选参数，支持的可选参数包括 self::GROUP_ID, self::MESSAGE_EXPIRES
     * @return 成功：PHP数组；失败：false 
     */
    public function pushGroupMsg($messages, $msgKeys, $deviceType, $optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::MESSAGES, self::MSG_KEYS, self::DEVICE_TYPE), $tmpArgs);
            $arrArgs[self::METHOD] = 'push_group_msg';
        	if(is_array($arrArgs [ self::MESSAGES ])) {
				$arrArgs [ self::MESSAGES ] = json_encode($arrArgs [ self::MESSAGES ]);
			}
			if(is_array($arrArgs [ self::MSG_KEYS ])) {
				$arrArgs [ self::MSG_KEYS ] = json_encode($arrArgs [ self::MSG_KEYS ]);
			}
            return $this->_commonProcess($arrArgs, array(self::MESSAGES, self::MSG_KEYS, self::DEVICE_TYPE));
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }
    
    /**
     * fetchGroupMsg: 查询用户广播组消息
     * 
     * 用户关注: 是
     *
     * @param array $optional可选参数，支持的可选参数包括 self::USER_ID, self::CHANNEL_ID, self::GROUP_ID, self::PAGENO, self::LIMIT
     * @return 成功：PHP数组；失败：false 
     */
    public function fetchGroupMsg($optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(), $tmpArgs);
            $arrArgs[self::METHOD] = 'fetch_group_msg';
            return $this->_commonProcess($arrArgs, array());
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }
    
    /**
     * fetchGroupMsgcount: 查询用户广播组消息个数
     * 
     * 用户关注: 是
     *
     * @param array $optional可选参数，支持的可选参数包括 self::USER_ID, self::CHANNEL_ID, self::GROUP_ID
     * @return 成功：PHP数组；失败：false 
     */
    public function fetchGroupMsgcount($optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(), $tmpArgs);
            $arrArgs[self::METHOD] = 'fetch_group_msgcount';
            return $this->_commonProcess($arrArgs, array());
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }
    
    /**
     * deleteGroupMsg: 删除广播组消息
     * 
     * 用户关注: 是
     *
     * @param json string $msgIds,如果是数组格式，则会自动做json_encode;
     * @param array $optional可选参数，支持的可选参数包括 self::GROUP_ID
     * @return 成功：PHP数组；失败：false  
     */
    public function deleteGroupMsg($msgIds, $optional = null)
    {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::MSG_IDS), $tmpArgs);
            $arrArgs[self::METHOD] = 'delete_group_msg';
        	if(is_array($arrArgs [ self::MSG_IDS ])) {
				$arrArgs [ self::MSG_IDS ] = json_encode($arrArgs [ self::MSG_IDS ]);
			}
            return $this->_commonProcess($arrArgs, array(self::MSG_IDS));
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }


	/**
	 * initAppIoscert: 初始化应用ios证书
	 * 
	 * 用户关注: 是
	 *
	 * @param string $name 证书名称
	 * @param string description 证书描述
	 * @param string $cert 证书内容
	 * @param array $optional
	 * @return 成功：PHP数组；失败：false  
	 */
	public function initAppIoscert($name, $description, $release_cert, $dev_cert, $optional = null)
	{		
		$this->_resetErrorStatus();
		try {
			$tmpArgs = func_get_args();
			$arrArgs = $this->_mergeArgs(array(self::NAME, self::DESCRIPTION, self::RELEASE_CERT, self::DEV_CERT), $tmpArgs);
			$arrArgs[self::METHOD] = "init_app_ioscert";
			return $this->_commonProcess($arrArgs, array(self::NAME, self::DESCRIPTION, self::RELEASE_CERT, self::DEV_CERT));
		} catch(Exception $ex) {
			$this->_channelExceptionHandler($ex);
			return false;
		}
	}

	/**
	 * updateAppIoscert: 修改ios证书内容
	 * 
	 * 用户关注: 是
	 *
	 * @param array $optional可选参数，支持的可选参数包括 self::NAME, self::DESCRIPTION, self::CERT
	 * @return 成功：PHP数组；失败：false   
	 */
	public function updateAppIoscert($optional = null)
	{		
		$this->_resetErrorStatus();
		try {
			$tmpArgs = func_get_args();
			$arrArgs = $this->_mergeArgs(array(), $tmpArgs);
			$arrArgs[self::METHOD] = "update_app_ioscert";
			return $this->_commonProcess($arrArgs, array());	
		} catch(Exception $ex) {
			$this->_channelExceptionHandler($ex);
			return false;
		}
	}

	/**
	 * queryAppIoscert: 查询ios证书内容
	 * 
	 * 用户关注: 是
	 *
	 * @param array $optional
	 * @return 成功：PHP数组；失败：false   
	 */
	public function queryAppIoscert($optional = null)
	{
		$this->_resetErrorStatus();
		try {
			$tmpArgs = func_get_args();
			$arrArgs = $this->_mergeArgs(array(), $tmpArgs);
			$arrArgs[self::METHOD] = "query_app_ioscert";	
			return $this->_commonProcess($arrArgs, array()); 
		} catch(Exception $ex) {
			$this->_channelExceptionHandler($ex);
			return false;
		}
	}

	/**
	 * destroyAppIoscert: 删除ios证书内容
	 * 
	 * 用户关注: 是
	 *
	 * @param array $optional
	 * @return 成功：PHP数组；失败：false   
	 */
	public function destroyAppIoscert($optional = null)
	{
		$this->_resetErrorStatus();
		try {
			$tmpArgs = func_get_args();
			$arrArgs = $this->_mergeArgs(array(), $tmpArgs);
			$arrArgs[self::METHOD] = "destroy_app_ioscert";
			return $this->_commonProcess($arrArgs, array());
		} catch(Exception $ex) {
			$this->_channelExceptionHandler($ex);
			return false;
		}
	}
	
	/**
	 * pushAndroidMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId、channelId推送离线消息，仅支持android设备
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $channelId 用户channel的ID号
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode;如果是json格式给出，必须与$msgIds对应起来;
	 * @param string $msgKeys 发送的消息key，如果是数组格式，则会自动做json_encode;如果是json字符串格式给出，必须与$messages对应起来;
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::MESSAGE_TYPE、Channel::MESSAGE_EXPIRES
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function pushAndroidMessage ( $userId, $channelId, $messages, $msgKeys, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'push_android_msg';
			if(is_array($arrArgs [ self::MESSAGES ])) {
				$arrArgs [ self::MESSAGES ] = json_encode($arrArgs [ self::MESSAGES ]);
			}
			if(is_array($arrArgs [ self::MSG_KEYS ])) {
				$arrArgs [ self::MSG_KEYS ] = json_encode($arrArgs [ self::MSG_KEYS ]);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * pushBrowserMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId、channelId推送离线消息，仅支持browser设备
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $channelId 用户channel的ID号
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode;如果是json格式给出，必须与$msgIds对应起来;
	 * @param string $msgKeys 发送的消息key，如果是数组格式，则会自动做json_encode;如果是json字符串格式给出，必须与$messages对应起来;
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::MESSAGE_TYPE、Channel::MESSAGE_EXPIRES
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function pushBrowserMessage ( $userId, $channelId, $messages, $msgKeys, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'push_browser_msg';
			if(is_array($arrArgs [ self::MESSAGES ])) {
				$arrArgs [ self::MESSAGES ] = json_encode($arrArgs [ self::MESSAGES ]);
			}
			if(is_array($arrArgs [ self::MSG_KEYS ])) {
				$arrArgs [ self::MSG_KEYS ] = json_encode($arrArgs [ self::MSG_KEYS ]);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * pushPcMessage
	 * 
	 * 用户关注：是
	 * 
	 * 根据userId、channelId推送离线消息，仅支持pc设备
	 * 
	 * @access public
	 * @param string $userId 用户ID号
	 * @param string $channelId 用户channel的ID号
	 * @param string $messages 要发送的消息，如果是数组格式，则会自动做json_encode;如果是json格式给出，必须与$msgIds对应起来;
	 * @param string $msgKeys 发送的消息key，如果是数组格式，则会自动做json_encode;如果是json字符串格式给出，必须与$messages对应起来;
	 * @param array $optional 可选参数，支持的可选参数包括：Channel::MESSAGE_TYPE、Channel::MESSAGE_EXPIRES
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function pushPcMessage ( $userId, $channelId, $messages, $msgKeys, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'push_pc_msg';
			if(is_array($arrArgs [ self::MESSAGES ])) {
				$arrArgs [ self::MESSAGES ] = json_encode($arrArgs [ self::MESSAGES ]);
			}
			if(is_array($arrArgs [ self::MSG_KEYS ])) {
				$arrArgs [ self::MSG_KEYS ] = json_encode($arrArgs [ self::MSG_KEYS ]);
			}
			return $this->_commonProcess ( $arrArgs, array ( self::USER_ID, self::CHANNEL_ID, self::MESSAGES, self::MSG_KEYS ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}
	
	/**
	 * queryDeviceType
	 * 
	 * 用户关注：是
	 * 
	 * 根据channelId查询设备类型
	 * 
	 * @access public
	 * @param string $channelId 用户channel的ID号
	 * @return 成功：PHP数组；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	public function queryDeviceType ( $channelId, $optional = NULL ) 
	{
		$this->_resetErrorStatus (  );
		try 
		{
			$tmpArgs = func_get_args (  );
			$arrArgs = $this->_mergeArgs ( array ( self::CHANNEL_ID ), $tmpArgs );
			$arrArgs [ self::METHOD ] = 'query_device_type';
			return $this->_commonProcess ( $arrArgs, array ( self::CHANNEL_ID ) );
		} 
		catch ( Exception $ex ) 
		{
			$this->_channelExceptionHandler ( $ex );
			return false; 
		}
	}

	/**
	 * __construct
	 *  
	 * 用户关注：是
	 * 
	 * 对象构造方法，用户可以传入$accessToken进行初始化
	 * 如果用户没有传入$accessToken，这三个参数可以其他几个地方予以设置，如下：
	 * 1. 在调用SDK时，在$optional参数中设置，如$optional[self::ACCESS_TOKEN] = 'my_access_token'，影响范围：本次SDK调用
	 * 2. 调用SDK对象的setXXX系列函数进行设置，如$Channel->setAccessToken('my_access_token')，影响范围：自设置之后起的每次SDK调用
	 * 3. 全局变量，如g_accessToken = 'my_access_token'，影响范围：当1、2均无法获取到$accessToken时，会从全局变量中获取
	 * 说明：SDK获取$accessToken的优先级是：
	 * 1. SDK的$optional参数
	 * 2. Channel对象的属性（通过初始化参数或setXXX系列函数指定）
	 * 3. 全局变量
	 * 
	 * @access public
	 * @param string $accessToken
	 * @throws ChannelException 如果出错，则抛出异常，异常号是self::CHANNEL_SDK_INIT_FAIL
	 * 
	 * @version 1.0.0.0
	 */
	public function __construct ($accessToken = NULL, $arr_curlOpts = array()) 
	{
		if (is_null($accessToken) || $this->_checkString($accessToken, 1, 256)) {
			$this->_accessToken = $accessToken;
		} else {
			throw new ChannelException("invalid param - access_token[$accessToken],"
                    . "which must be a 1 - 256 length string",
                    self::CHANNEL_SDK_INIT_FAIL );
		}
		
		if (!is_array($arr_curlOpts)) {
			throw new ChannelException('invalid param - arr_curlopt is not an array ['
                    . print_r($arr_curlOpts, true) . ']',
                    self::CHANNEL_SDK_INIT_FAIL);
		}
        $this->_curlOpts = array_merge($this->_curlOpts, $arr_curlOpts);
        
		$this->_resetErrorStatus();
	}

	/**
	 * _checkString
	 *  
	 * 用户关注：否
	 * 
	 * 检查参数是否是一个大于等于$min且小于等于$max的字符串
	 * 
	 * @access protected
	 * @param string $str 要检查的字符串
	 * @param int $min 字符串最小长度
	 * @param int $max 字符串最大长度
	 * @return 成功：true；失败：false
	 * 
	 * @version 1.0.0.0
	 */
	protected function _checkString($str, $min, $max)
	{
		if (is_string($str) && strlen($str) >= $min && strlen($str) <= $max) {
			return true;
		}
		return false;
	}

	/**
	 * _getKey
	 * 
	 * 用户关注：否
	 * 获取AK/SK/TOKEN/HOST的统一过程函数
	 * 
	 * @access protected
	 * @param array $opt 参数数组
	 * @param string $opt_key 参数数组的key
	 * @param string $member 对象成员
	 * @param string $g_key 全局变量的名字
	 * @param string $env_key 环境变量的名字
	 * @param int $min 字符串最短值
	 * @param int $max 字符串最长值
	 * @throws ChannelException 如果出错，则抛出ChannelException异常，异常类型为self::CHANNEL_SDK_PARAM
	 * 
	 * @version 1.0.0.0
	 */
	protected function _getKey(&$opt,
            $opt_key,
            $member,
            $g_key,
            $env_key,
            $min,
            $max,
            $throw = true)
	{
		$dis = array(
            'access_token' => 'access_token',
            );
		global $$g_key;
		if (isset($opt[$opt_key])) {
			if (!$this->_checkString($opt[$opt_key], $min, $max)) {
				throw new ChannelException ( 'invalid ' . $dis[$opt_key] . ' in $optinal ('
                        . $opt[$opt_key] . '), which must be a ' . $min . '-' . $max
                        . ' length string', self::CHANNEL_SDK_PARAM );
			}
			return;
		}
		if ($this->_checkString($member, $min, $max)) {
			$opt[$opt_key] = $member;
			return;
		}
		if (isset($$g_key)) {
			if (!$this->_checkString($$g_key, $min, $max)) {
				throw new ChannelException('invalid ' . $g_key . ' in global area ('
                        . $$g_key . '), which must be a ' . $min . '-' . $max
                        . ' length string', self::CHANNEL_SDK_PARAM);
			}
			$opt[$opt_key] = $$g_key;
			return;
		}
		
		if (false !== getenv($env_key)) {
			if (!$this->_checkString(getenv($env_key), $min, $max)) {
				throw new ChannelException( 'invalid ' . $env_key . ' in environment variable ('
                        . getenv($env_key) . '), which must be a ' . $min . '-' . $max
                        . ' length string', self::CHANNEL_SDK_PARAM);
			}
			$opt[$opt_key] = getenv($env_key) ;
			return;
		}
		
		if ($opt_key === self::HOST) {   
            $opt[$opt_key] = self::DEFAULT_HOST;
			return;
        }
		if ($throw) {
			throw new ChannelException('no param (' . $dis[$opt_key] . ') was found',
                    self::CHANNEL_SDK_PARAM);
		}
	}

	/**
	 * _adjustOpt
	 *   
	 * 用户关注：否
	 * 
	 * 参数调整方法
	 * 
	 * @access protected
	 * @param array $opt 参数数组
	 * @throws ChannelException 如果出错，则抛出异常，异常号为 self::CHANNEL_SDK_PARAM
	 * 
	 * @version 1.0.0.0
	 */
	protected function _adjustOpt(&$opt)
    {
		if (!isset($opt) || empty($opt) || !is_array($opt)) {
			throw new ChannelException('no params are set',self::CHANNEL_SDK_PARAM);
		}
		if (!isset($opt[self::TIMESTAMP])) {
			$opt[self::TIMESTAMP] = time();
		}
        
		$this->_getKey($opt, self::HOST, null, 'g_host',
                'HTTP_BAE_ENV_ADDR_CHANNEL', 1, 1024);
        
		$this->_getKey($opt, self::ACCESS_TOKEN, $this->_accessToken,
                'g_accessToken', 'HTTP_BAE_ENV_ACCESS_TOKEN', 1, 256, false);
        
		if (isset($opt[self::ACCESS_KEY])) {
			unset($opt[self::ACCESS_KEY]);
		}
		if (isset($opt[self::SECRET_KEY])) {
			unset($opt[self::SECRET_KEY]);
		}
	}

	/**
	 * _channelServerGetSign
	 *   
	 * 用户关注：否
	 * 
	 * 签名方法
	 * 
	 * @access protected
	 * @param array $opt 参数数组
	 * @param array $arrContent 可以加入签名的参数数组，返回值
	 * @param array $arrNeed 必须的参数
	 * @throws ChannelException 如果出错，则抛出异常，异常号为self::CHANNEL_SDK_PARAM
	 * 
	 * @version 1.0.0.0
	 */
	protected function _channelServerGetSign(&$opt, &$arrContent, $arrNeed = array())
    {
		$arrData = array();
		$arrContent = array();
        
		$arrNeed[] = self::TIMESTAMP;
		$arrNeed[] = self::METHOD;
		$arrNeed[] = self::ACCESS_TOKEN;
		if (isset($opt[self::EXPIRES])) {
			$arrNeed[] = self::EXPIRES;
		}
		if (isset($opt[self::VERSION])) {
			$arrNeed[] = self::VERSION;
		}
        
		$arrExclude = array(self::CHANNEL_ID, self::HOST, self::SECRET_KEY);
		foreach ($arrNeed as $key) {
			if (!isset($opt[$key]) || (!is_integer($opt[$key]) && empty($opt[$key]))) {
				throw new ChannelException ("lack param (${key})",
                        self::CHANNEL_SDK_PARAM );
			}
			if (in_array($key, $arrExclude)) {
				continue;
			}
			$arrData[$key] = $opt[$key];
			$arrContent[$key] = $opt [$key];
		}
		foreach ($opt as $key => $value) {
			if (!in_array($key, $arrNeed) && !in_array($key, $arrExclude)) {
				$arrData[$key] = $value;
				$arrContent[$key] = $value;
			}
		}
		if (isset($opt[self::CHANNEL_ID]) && !is_null($opt[self::CHANNEL_ID])) {
			$arrContent[self::CHANNEL_ID] = $opt[self::CHANNEL_ID];
		}
		$arrContent[self::HOST] = $opt[self::HOST];
	}

	/**
	 * _baseControl
	 *   
	 * 用户关注：否
	 * 
	 * 网络交互方法
	 * 
	 * @access protected
	 * @param array $opt 参数数组
	 * @throws ChannelException 如果出错，则抛出异常，错误号为self::CHANNEL_SDK_SYS
	 * 
	 * @version 1.0.0.0
	 */
	protected function _baseControl($opt)
	{
		$content = '';
		$resource = 'channel';
		if (isset($opt[self::CHANNEL_ID]) && !is_null($opt[self::CHANNEL_ID])) {
			$resource = $opt[self::CHANNEL_ID];
			unset($opt[self::CHANNEL_ID]);
		}
		$host = $opt[self::HOST];
		unset($opt[self::HOST]);
		foreach ($opt as $k => $v) {
			if (is_string($v)) {
				$v = urlencode($v);
			}
			$content .= $k . '=' . $v . '&';
		}
		$content = substr($content, 0, strlen($content) - 1);
		$url = 'https://' . $host . '/rest/2.0/' . self::PRODUCT . '/';
		$url .= $resource;
		$request = new RequestCore($url);
		$headers['Content-Type'] = 'application/x-www-form-urlencoded';
		$headers['User-Agent'] = 'Baidu Channel Service Phpsdk Client';
		foreach ($headers as $headerKey => $headerValue) {
			$headerValue = str_replace(array("\r", "\n"), '', $headerValue);
			if($headerValue !== '') {
				$request->add_header($headerKey, $headerValue);
			}
		}
		$request->set_method('POST');
		$request->set_body($content);
		if (is_array($this->_curlOpts)) {
			$request->set_curlOpts($this->_curlOpts);
		}
		$request->send_request();
		return new ResponseCore($request->get_response_header(),
                $request->get_response_body(),
                $request->get_response_code());
	}

	/**
	 * _channelExceptionHandler
	 *   
	 * 用户关注：否
	 * 
	 * 异常处理方法
	 * 
	 * @access protected
	 * @param Excetpion $ex 异常处理函数，主要是填充Channel对象的错误状态信息
	 * 
	 * @version 1.0.0.0
	 */
	protected function _channelExceptionHandler($ex)
	{
		$tmpCode = $ex->getCode();
		if (0 === $tmpCode) {
			$tmpCode = self::CHANNEL_SDK_SYS;
		}

		$this->errcode = $tmpCode;
		if ($this->errcode >= 30000) {
			$this->errmsg = $ex->getMessage();
		} else {	
			$this->errmsg = $this->_arrayErrorMap[$this->errcode] . ',detail info['
                    . $ex->getMessage() . ',break point:' . $ex->getFile() . ':'
                    . $ex->getLine() . '].';
		}
	}

	/**
	 * _commonProcess
	 *   
	 * 用户关注：否
	 * 
	 * 所有服务类SDK方法的通用过程
	 * 
	 * @access protected
	 * @param array $paramOpt 参数数组
	 * @param array $arrNeed 必须的参数KEY
	 * @throws ChannelException 如果出错，则抛出异常
	 * 
	 * @version 1.0.0.0
	 */
	protected function _commonProcess($paramOpt = NULL, $arrNeed = array())
	{
		$this->_adjustOpt($paramOpt);
		$arrContent = array();
		$this->_channelServerGetSign($paramOpt, $arrContent, $arrNeed);
		$ret = $this->_baseControl($arrContent);
		if (empty($ret)) {
			throw new ChannelException('base control returned empty object',
                    self::CHANNEL_SDK_SYS);
		}
		if ($ret->isOK()) {
			$result = json_decode($ret->body, true);
			if (is_null($result)) {
				throw new ChannelException($ret->body,
                        self::CHANNEL_SDK_HTTP_STATUS_OK_BUT_RESULT_ERROR);
			}
			$this->_requestId = $result['request_id'];
			return $result;
		}
		$result = json_decode($ret->body,true);
		if (is_null($result)) {
			throw new ChannelException('ret body:' . $ret->body,
                    self::CHANNEL_SDK_HTTP_STATUS_ERROR_AND_RESULT_ERROR);
		}
		$this->_requestId = $result['request_id'];
		throw new ChannelException($result['error_msg'], $result['error_code']);
	}

	/**
	 * _mergeArgs
	 *   
	 * 用户关注：否
	 * 
	 * 合并传入的参数到一个数组中，便于后续处理
	 * 
	 * @access protected
	 * @param array $arrNeed 必须的参数KEY
	 * @param array $tmpArgs 参数数组
	 * @throws ChannelException 如果出错，则抛出异常，异常号为self::Channel_SDK_PARAM 
	 * 
	 * @version 1.0.0.0
	 */
	protected function _mergeArgs($arrNeed, $tmpArgs)
	{
		$arrArgs = array();
		if (0 == count($arrNeed) && 0 == count($tmpArgs)) {
			return $arrArgs;
		}
		if (count($tmpArgs) - 1 != count($arrNeed) && count($tmpArgs) != count($arrNeed)) {
			$keys = '(';
			foreach ($arrNeed as $key) {
                $keys .= $key .= ',';
			}
			if ($keys[strlen($keys) - 1] === '' && ',' === $keys[strlen($keys) - 2]) {
				$keys = substr($keys, 0, strlen($keys) - 2);
			}
			$keys .= ')';
			throw new Exception('invalid sdk params, params' . $keys . 'are needed',
                    self::CHANNEL_SDK_PARAM);
		}
		if (count($tmpArgs) - 1 == count($arrNeed) && !is_array($tmpArgs[count($tmpArgs) - 1])) {
			throw new Exception('invalid sdk params, optional param must be an array',
                    self::CHANNEL_SDK_PARAM);
		}

		$idx = 0;
		foreach ($arrNeed as $key) {
			if (!is_integer($tmpArgs[$idx]) && empty($tmpArgs[$idx])) {
				throw new Exception("lack param (${key})", self::CHANNEL_SDK_PARAM);
			}
			$arrArgs[$key] = $tmpArgs[$idx];
			$idx += 1;
		}
		if (isset($tmpArgs[$idx])) {
			foreach ($tmpArgs[$idx] as $key => $value) {
				if ( !array_key_exists($key, $arrArgs) && (is_integer($value) || !empty($value))) {
					$arrArgs[$key] = $value;
				}
			}
		}
		if (isset($arrArgs[self::CHANNEL_ID])) {
			$arrArgs[self::CHANNEL_ID] = urlencode($arrArgs[self::CHANNEL_ID]);
		}
		return $arrArgs;
	}

	/**
	 * _resetErrorStatus
	 *   
	 * 用户关注：否
	 * 
	 * 恢复对象的错误状态，每次调用服务类方法时，由服务类方法自动调用该方法
	 * 
	 * @access protected
	 * 
	 * @version 1.0.0.0
	 */
	protected function _resetErrorStatus()
	{
		$this->errcode = 0;
		$this->errmsg = $this->_arrayErrorMap[$this->errcode];
		$this->_requestId = 0;
	}
}
