<?php
/***************************************************************************
 *
 * Copyright (c) 2011 Baidu.com, Inc. All Rights Reserved
 *
 **************************************************************************/

/**
 * Baidu.php
 * 
 * 
 * @package	Baidu
 * @author	wulin02(wulin02@baidu.com)
 * @version	$Revision: 1.0 Mon Jun 27 10:52:18 CST 2011
 **/

// In PHP 5.2 or higher we don't need to bring this in
if (!function_exists('json_encode')) {
	
	require_once(dirname(__FILE__) . '/JSON.php');

	function json_encode($value)
	{
		$services_json = new Services_JSON();
		return $services_json->encode($value);
	}

	function json_decode($json, $assoc = false)
	{
		$services_json = new Services_JSON($assoc ? SERVICES_JSON_LOOSE_TYPE : 0);
		return $services_json->decode($json);
	}
}

require_once(dirname(__FILE__) . '/BaiduStore.php');

// when using bae(baidu app engine) to deploy the application,
// just uncomment the following two lines.
//require_once('app_config.php');
//require_once(BAE_API_PATH . '/BaeFetchUrl.class.php'); 

class Baidu
{
    /**
     * URL prefix definition of Baidu OpenAPI interfaces.
     */
    public static $BD_OPENAPI_URL_PREFIXS = array(
    	'public'	=> 'http://openapi.baidu.com/public/2.0/',
    	'https'		=> 'https://openapi.baidu.com/rest/2.0/',
    	'http'		=> 'http://openapi.baidu.com/rest/2.0/',
    );
	
    /**
     * Endpoints definition of Baidu OAuth2.0.
     */
    public static $BD_OAUTH2_ENDPOINTS = array(
    	'authorize'	=> 'https://openapi.baidu.com/oauth/2.0/authorize',
    	'token'		=> 'https://openapi.baidu.com/oauth/2.0/token',
    	'logout'	=> 'https://openapi.baidu.com/connect/2.0/logout',
    );

	/**
	 * List of query parameters that get automatically dropped when rebuilding
	 * the current URL.
	 */
	protected static $DROP_QUERY_PARAMS = array(
		'code',
		'state',
	);
	
	/**
	 * Default options for curl.
	 */
	protected $curlOpts = array(
		CURLOPT_CONNECTTIMEOUT	=> 3,
		CURLOPT_TIMEOUT			=> 5,
		CURLOPT_USERAGENT		=> 'baidu-restclient-php-2.0',
    	CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_1,
    	CURLOPT_RETURNTRANSFER	=> true,
    	CURLOPT_HEADER			=> false,
    	CURLOPT_FOLLOWLOCATION	=> false,
	);
  
    protected $apiKey;
    protected $apiSecret;
    
    /**
     * @var BaiduStore
     */
    protected $store = null;
    
    protected $state = null;
    
    /**
     * @var array
     */
    protected $session = null;
    
    protected $format = 'json';
    protected $httpMethod = 'GET';
    protected $useHttps = false;
    protected $finalEncode = 'UTF-8';
    
    protected $errcode = 0;
    protected $errmsg = '';
    
    public function __construct($apiKey, $apiSecret, $store = null)
    {
    	$this->apiKey = $apiKey;
    	$this->apiSecret = $apiSecret;
    	$this->setStore($store);
    }
    
    public function errcode()
    {
    	return $this->errcode;
    }
    
    public function errmsg()
    {
    	return $this->errmsg;
    }
    
    /**
     * Get Baidu Open API response format
     * 
     * @return string The open api response format
     */
    public function getFormat()
    {
    	return $this->format;
    }
    
    /**
     * Set the format of Baidu Open API response
     * 
     * @param $format	open api response format, 'json' or 'xml'
     * @return BaseBaidu
     */
    public function setFormat($format)
    {
    	$this->format = $format;
    	return $this;
    }
    
    /**
     * Get the final encoding of the client.
     * 
     * @return string Final encoding of the client.
     */
    public function getFinalEncode()
    {
    	return $this->finalEncode;
    }
    
    /**
     * Set the final encoding of the client.
     * 
     * @param string $finalEncode 'UTF-8' or 'GBK'
     * @return BaseBaidu
     */
    public function setFinalEncode($finalEncode)
    {
    	$this->finalEncode = $finalEncode;
    	return $this;
    }
    
    /**
     * Check whether to use POST method for openapi calls.
     * 
     * @return bool Returns true if use POST method, or false if not.
     */
    public function isUsingPost()
    {
    	return $this->httpMethod == 'POST';
    }
    
    /**
     * Set whether to use POST method or not for openapi calls.
     * 
     * @param bool $flag true if want to use POST, otherwise use false
     * @return BaseBaidu
     */
    public function usePost($flag = true)
    {
    	$this->httpMethod = $flag ? 'POST' : 'GET';
    	return $this;
    }
    
    /**
     * Check whether to use https or not for openapi calls.
     * 
     * @return bool Returns true if use https, or false if not.
     */
    public function isUsingHttps()
    {
    	return $this->useHttps;
    }
    
    /**
     * Set whether to use https or not for openapi calls.
     * 
     * @param bool $flag true if want to use https, otherwise use false
     * @return BaseBaidu
     */
    public function useHttps($flag = true)
    {
    	$this->useHttps = true;
    	return $this;
    }
    
	/**
     * Set the session data storage instance.
     * @param BaiduStore $store
     */
    public function setStore($store)
    {
    	$this->store = $store;
    	if ($this->store) {
    		$state = $this->store->get('state');
    		if (!empty($state)) {
    			$this->state = $state;
    		}
    		$this->getSession();
    		$this->establishCSRFTokenState();
    	}
    	
    	return $this;
    }
    
    /**
     * Get user session.
     * 
     * @return array 
     */
	public function getSession()
	{
		if ($this->session === null) {
			$this->session = $this->doGetSession();
		}
		
		return $this->session;
	}
	
	/**
	 * Set user session.
	 * 
	 * @param array $session	User session info.
	 * @return BaseBaidu
	 */
	public function setSession($session)
	{
		$this->session = $session;
		if ($session) {
			$this->store->set('session', $session);
		} else {
			$this->store->remove('session');
		}
		return $this;
	}
	
	/**
	 * Get access token for openapi calls based on https.
	 * 
	 * @return mixed Returns access token if user has authorized the app, or false if not.
	 */
	public function getAccessToken()
	{
		$session = $this->getSession();
		if (isset($session['access_token'])) {
			return $session['access_token'];
		} else {
			return false;
		}
	}
	
	/**
	 * Get refresh token.
	 * 
	 * @return mixed Returns refresh token if app has, or false if not.
	 */
	public function getRefreshToken()
	{
		$session = $this->getSession();
		if (isset($session['refresh_token'])) {
			return $session['refresh_token'];
		} else {
			return false;
		}
	}
	
	/**
	 * Get session key for openapi calls based on http.
	 * 
	 * @return mixed Returns session key if user has authorized the app, or false if not.
	 */
	public function getSessionKey()
	{
		$session = $this->getSession();
		if (isset($session['session_key'])) {
			return $session['session_key'];
		} else {
			return false;
		}
	}
	
	/**
	 * Get session secret for openapi calls based on http.
	 * 
	 * @return mixed Returns session secret if user has authorized the app, of false if not.
	 */
	public function getSessionSecret()
	{
		$session = $this->getSession();
		if (isset($session['session_secret'])) {
			return $session['session_secret'];
		} else {
			return false;
		}
	}
	
	/**
	 * Get currently logged in user's uid.  
	 */
	public function getLoggedInUser()
	{
		// Get user from cached data or from access token
		$user = $this->getUser();
		
		// If there's bd_sig & bd_user parameter in query parameters,
		// it must be an inside web app(app on baidu) loading request,
		// then we must check whether the uid passed from baidu is the
		// same as we get from persistent data or from access token, 
		// if it's not, we should clear all the persistent data and to 
		// get an access token again.
		if (isset($_REQUEST['bd_sig']) && isset($_REQUEST['bd_user'])) {
			$sig = self::generateSign(
				array('bd_user' => $_REQUEST['bd_user']), 
				$this->apiSecret, 'bd_sig');
			if ($sig != $_REQUEST['bd_sig'] ||
				$user['uid'] != $_REQUEST['bd_user']) {
				$this->store->remove('session');
			}
		}
		
		return $user;
	}

	/**
	 * Get a Login URL for use with redirects. By default, full page redirect is
	 * assumed. If you are using the generated URL with a window.open() call in
	 * JavaScript, you can pass in display=popup as part of the $params.
	 *
	 * The parameters:
	 * - response_type: 'token' or 'code'
	 * - redirect_uri: the url to go to after a successful login
	 * - scope: blank space separated list of requested extended perms
	 * - display: login page style, 'page', 'popup', 'touch' or 'mobile'
	 *
	 * @param Array $params provide custom parameters
	 * @return String the URL for the login flow
	 */
	public function getLoginUrl($params = array())
	{
		$currentUrl = $this->getCurrentUrl();
		
		$params = array_merge(array('response_type'	=> 'token',
									'client_id'		=> $this->apiKey,
									'redirect_uri'	=> $currentUrl,
									'scope'			=> '',
									'state'			=> $this->state,
									'display'		=> 'page',
									), $params);
		return self::$BD_OAUTH2_ENDPOINTS['authorize'] . '?' . http_build_query($params, '', '&');
	}
	
	/**
	 * Get a Logout URL suitable for use with redirects.
	 * 
	 * The parameters:
	 * - next: the url to go to after a successful logout
	 * - access_token: the access token for current user
	 *   
	 * @param Array $params provide custom parameters
	 * @return String the URL for the logout flow
	 */
	public function getLogoutUrl($params = array())
	{
		$params = array_merge(array('access_token' => $this->getAccessToken(),
									'next' => $this->getCurrentUrl()), $params);
		return self::$BD_OAUTH2_ENDPOINTS['logout'] . '?' . http_build_query($params, '', '&');
	}
    
    /*******************************************************
     * 
     * 		Baidu OAuth2.0 Service Related Interfaces
     * 
     ******************************************************/
    
	/**
	 * Get baidu oauth2's authorization granting url.
	 * 
	 * @param string $response_type	Response type, 'code' or 'token'
	 * @param string $redirect_uri	The url to go after user authorize the app
	 * @param string $scope		Extend permissions delimited by blank space
	 * @param string $display	Authorization page style, 'page', 'popup', 'touch' or 'mobile'
	 * @param string $state		state parameter
	 * @return string Page url for authorization granting
	 */
	public function getAuthorizeUrl($response_type = 'code', $redirect_uri = '', $scope = '', $display = 'popup', $state = '')
	{		
		$params = array(
			'client_id'		=> $this->apiKey,
			'response_type'	=> $response_type,
			'redirect_uri'	=> $redirect_uri ? $redirect_uri : $this->getCurrentUrl(),
			'scope'			=> $scope,
			'state'			=> $state ? $state : $this->state,
			'display'		=> $display,
		);
		return self::$BD_OAUTH2_ENDPOINTS['authorize'] . '?' . http_build_query($params, '', '&');
	}
	
	/**
	 * Get access token ifno by authorization code.
	 * 
	 * @param string $code	Authorization code
	 * @param string $redirect_uri The redirect uri used when getting authorization code
	 * @return mixed returns access token info if success, or false if failed
	 */
	public function getAccessTokenByAuthorizationCode($code, $redirect_uri = '')
	{
		$params = array(
			'grant_type'	=> 'authorization_code',
			'code'			=> $code,
			'client_id'		=> $this->apiKey,
			'client_secret'	=> $this->apiSecret,
			'redirect_uri'	=> $redirect_uri ? $redirect_uri : $this->getCurrentUrl(),
		);
		return $this->makeAccessTokenRequest($params);
	}
	
	/**
	 * Get access token info by password credentials
	 * 
	 * @param string $username	User name
	 * @param string $password	User password
	 * @param string $scope		Extend permissions delimited by blank space
	 * @return mixed returns access token info if success, or false if failed
	 */
	public function getAccessTokenByPasswordCredentials($username, $password, $scope = '')
	{
		$params = array(
			'grant_type'	=> 'password',
			'username'		=> $username,
			'password'		=> $password,
			'client_id'		=> $this->apiKey,
			'client_secret'	=> $this->apiSecret,
			'scope'			=> $scope,
		);
		return $this->makeAccessTokenRequest($params);
	}
	
	/**
	 * Get access token info by client credentials.
	 * 
	 * @param string $scope		Extend permissions delimited by blank space
	 * @return mixed returns access token info if success, or false if failed.
	 */
	public function getAccessTokenByClientCredentials($scope = '')
	{
		$params = array(
			'grant_type'	=> 'client_credentials',
			'client_id'		=> $this->apiKey,
			'client_secret'	=> $this->apiSecret,
			'scope'			=> $scope,
		);
		return $this->makeAccessTokenRequest($params);
	}
	
	/**
	 * Refresh access token by refresh token.
	 * 
	 * @param string $refresh_token The refresh token
	 * @param string $scope	Extend permissions delimited by blank space
	 * @return mixed returns access token info if success, or false if failed.
	 */
	public function getAccessTokenByRefreshToken($refresh_token, $scope = '')
	{
		$params = array(
			'grant_type'	=> 'refresh_token',
			'refresh_token'	=> $refresh_token,
			'client_id'		=> $this->apiKey,
			'client_secret'	=> $this->apiSecret,
			'scope'			=> $scope,
		);
		return $this->makeAccessTokenRequest($params);
	}
	
	/**
	 * Make an oauth access token request
	 * 
	 * The parameters:
	 * - client_id: The client identifier, just use api key
	 * - response_type: 'token' or 'code'
	 * - redirect_uri: the url to go to after a successful login
	 * - scope: The scope of the access request expressed as a list of space-delimited, case sensitive strings.
	 * - state: An opaque value used by the client to maintain state between the request and callback.
	 * - display: login page style, 'page', 'popup', 'touch' or 'mobile'
	 * 
	 * @param array $params	oauth request parameters
	 * @return mixed returns access token info if success, or false if failed
	 */
	public function makeAccessTokenRequest($params)
	{
		$result = $this->makeRequest(self::$BD_OAUTH2_ENDPOINTS['token'], $params, 'POST');
		if ($result) {
			$result = json_decode($result, true);
			if (isset($result['error_description'])) {
				$this->setError($result['error'], $result['error_description']);
				return false;
			}
			return $result;
		}
		
		return false;
	}
	
	/*******************************************************
     * 
     * 		Baidu OpenAPI Service Related Interfaces
     * 
     ******************************************************/
    /**
     * Call api which need authorization.
     * 
     * @param string $method api method name
     * @param array $params api specific parameters
     * @return mixed returns array if api call success, or false if failed
     */
    public function api($method, $params = array())
    {
    	if ($this->useHttps) {
    		return $this->makeHttpsApiCall($method, $params);
    	} else {
    		return $this->makeHttpApiCall($method, $params);
    	}
    }
    
    /**
     * Call public api which need not authorization.
     * 
     * @param string $method api method name
     * @param array $params api specific parameters
     * @return mixed returns array if api call success, or false if failed
     */
    public function publicApi($method, $params)
    {
    	return $this->makePublicApiCall($method, $params);
    }
    
    /**
     * Make api call by https request
     * 
     * @param string $method api method name
     * @param array $params api specific parameters
     * @return mixed returns array if api call success, or false if failed
     */
	public function makeHttpsApiCall($method, $params)
    {
    	$url = self::$BD_OPENAPI_URL_PREFIXS['https'] . $method;
    	
    	$params = array_merge(array('access_token' => $this->getAccessToken(),
    								'format' => $this->format), $params);
    	
    	return $this->makeApiCall($url, $params, $this->httpMethod);
    }
    
    /**
     * Make api call by http request
     * 
     * @param string $method api method name
     * @param array $params api specific parameters
     * @return mixed returns array if api call success, or false if failed
     */
    public function makeHttpApiCall($method, $params)
    {
    	$url = self::$BD_OPENAPI_URL_PREFIXS['http'] . $method;
    	
    	$params = array_merge(array('session_key' => $this->getSessionKey(),
    								'timestamp' => date('Y-m-d H:i:s', time()),
    								'format' => $this->format), $params);
    	$params['sign'] = self::generateSign($params, $this->getSessionSecret());
    	
    	return $this->makeApiCall($url, $params, $this->httpMethod);
    }
    
    /**
     * Make public api call by http request
     * 
     * @param string $method api method name
     * @param array $params api specific parameters
     * @return mixed returns array if api call success, or false if failed
     */
    public function makePublicApiCall($method, $params)
    {
    	$url = self::$BD_OPENAPI_URL_PREFIXS['public'] . $method;
    	
    	return $this->makeApiCall($url, $params, $this->httpMethod);
    }
    
    /**
     * Make an api call request
     * 
     * @param string $url Url for the specified api
     * @param array $params Parameters for the specified api
     * @param string $httpMethod http or https method, 'GET' or 'POST'
     * @return mixed returns array if api call success, or false if failed
     */
    public function makeApiCall($url, $params, $httpMethod = 'GET')
    {
    	$result = $this->makeRequest($url, $params, $httpMethod);
    	if ($result !== false) {
			if (strcasecmp($this->format, 'xml') === 0) {
				$result = $this->convertXml2Array($result);
			} else {
				$result = $this->converJson2Array($result);
			}
			if (is_array($result) && isset($result['error_code'])) {
				$this->setError($result['error_code'], $result['error_msg']);
				return false;
			}
    	}
		return $result;
    }
    
    /**
     * Make a http request
     * 
     * @param string $url Url to request
     * @param array $params Parameters for the request
     * @param string $httpMethod Http method, 'GET' or 'POST'
     * @return mixed returns string if the request success, or false if failed
     */
	public function makeRequest($url, $param, $httpMethod = 'GET')
    {
    	$useHttps = false;
    	if (stripos($url, 'https://') === 0) {
    		$useHttps = true;
    	}
    	
    	// when using bae(baidu app engine) to deploy the application,
    	// just comment the following line
    	$ch = curl_init();
    	// when using bae(baidu app engine) to deploy the application,
    	// and uncomment the following two lines
    	//$fetch= new BaeFetchUrl();
  		//$ch = $fetch->getHandle();
  		
    	$curl_opts = $this->curlOpts;

		if ($useHttps) {
			$curl_opts[CURLOPT_SSL_VERIFYPEER] = false;
		    //$curl_opts[CURLOPT_CAINFO] = 'ca-bundle1.crt';
		}
    	
    	//将数组转成url query
    	$param = http_build_query($param, '', '&');
    	if ($httpMethod == 'POST') {
    		$curl_opts[CURLOPT_URL] = $url;
    		$curl_opts[CURLOPT_POSTFIELDS] = $param;
    	} else {
    		$delimiter = strpos($url, '?') === false ? '?' : '&';
    		$curl_opts[CURLOPT_URL] = $url . $delimiter . $param;
    		$curl_opts[CURLOPT_POST] = false;
    	}
    
    	curl_setopt_array($ch, $curl_opts);
        $result = curl_exec($ch);
        
    	if ($result === false) {
    		$this->setError(curl_errno($ch), curl_error($ch));
            curl_close($ch);
            return false;
    	} elseif (empty($result)) {
    		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    		if ($http_code != 200) {
    			$this->setError($http_code, 'http response status code: ' . $http_code);
    			curl_close($ch);
    			return false;
    		}
    	}
        
    	curl_close($ch);
    	
    	return $result;
    }

	/*******************************************************
	 * 
	 * Utils functions
	 * 
	 ******************************************************/
	
	public static function generateSign($params, $secret, $namespace = 'sign')
    {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
        	if ($k != $namespace) {
        		$str .= "$k=$v";
        	}
        }
        $str .= $secret;
        return md5($str);
    }
	
	public static function iconv($var, $inCharset = 'UTF-8', $outCharset = 'GBK')
	{
		if (is_array($var)) {
			$rvar = array();
			foreach ($var as $key => $val) {
				$rvar[$key] = self::iconv($val, $inCharset, $outCharset);
			}
			return $rvar;
		} elseif (is_object($var)) {
			$rvar = null;
			foreach ($var as $key => $val) {
				$rvar->{$key} = self::iconv($val, $inCharset, $outCharset);
			}
			return $rvar;
		} elseif (is_string($var)) {
			return iconv($inCharset, $outCharset, $var);
		} else {
			return $var;
		}
	}
	
	public function getCurrentUrl()
	{
		$protocol = 'http://';
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
			$protocol = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) . '://';
		} elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$protocol = 'https://';
		}
		
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		} else {
			$host = $_SERVER['HTTP_HOST'];
		}

		$currentUrl = $protocol . $host . $_SERVER['REQUEST_URI'];
		$parts = parse_url($currentUrl);
		
		$query = '';
		if (!empty($parts['query'])) {
			// drop known oauth params
			$params = explode('&', $parts['query']);
			$retained_params = array();
			foreach ($params as $param) {
				if ($this->shouldRetainParam($param)) {
					$retained_params[] = $param;
				}
			}
			
			if (!empty($retained_params)) {
				$query = '?' . implode($retained_params, '&');
			}
		}
		
		// use port if non default
		$port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) ||
			 ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';
		
		// rebuild
		return $protocol . $parts['host'] . $port . $parts['path'] . $query;
	}
	
	/**
	 * Prints to the error log if you aren't in command line mode.
	 *
	 * @param String log message
	 */
	public static function errorLog($msg)
	{
		// disable error log if we are running in a CLI environment
		if (php_sapi_name() != 'cli') {
			error_log($msg);
		}
		// uncomment this if you want to see the errors on the page
		// print 'error_log: '.$msg."\n";
	}
	
	/**
	 * Get current user's uid and uname.
	 * 
	 * @return array array('uid' => xx, 'uname' => xx)
	 */
	protected function getUser()
	{
		$session = $this->getSession();
		if (isset($session['uid']) && isset($session['uname'])) {
			return array('uid' => $session['uid'], 'uname' => $session['uname']);
		} else {
			return false;
		}
	}
	
	protected function doGetSession()
	{
		// get authorization code from query parameters
		$code = $this->getCode();
		// check whether it is a CSRF attack request
		if ($code && $code != $this->store->get('code')) {
			$session = $this->getAccessTokenByAuthorizationCode($code);
			if ($session) {
				
				$this->store->set('code', $code);
				$this->setSession($session);
				$user = $this->api('passport/users/getLoggedInUser');
				if ($user) {
					$session = array_merge($session, $user);
					$this->setSession($session);
				}
				return $session;
			}
			
			// code was bogus, so everything based on it should be invalidated.
			$this->store->removeAll();
			return false;
		}
		
		// as a fallback, just return whatever is in the persistent store
		$session = $this->store->get('session');
		$this->setSession($session);
		if ($session && !isset($session['uid'])) {
			$user = $this->api('passport/users/getLoggedInUser');
			if ($user) {
				$session = array_merge($session, $user);
				$this->setSession($session);
			}
		}
		
		return $session;
	}

	/**
	 * Get the authorization code from the query parameters, if it exists,
	 * otherwise return false to signal no authorization code was discoverable.
	 *
	 * @return mixed Returns the authorization code, or false if the authorization
	 * code could not be determined.
	 */
	protected function getCode()
	{
		if (isset($_REQUEST['code'])) {
			if ($this->state !== null &&
				isset($_REQUEST['state']) &&
				$this->state === $_REQUEST['state']) {
				// CSRF state has done its job, so clear it
				$this->state = null;
				$this->store->remove('state');
				return $_REQUEST['code'];
			} else {
				self::errorLog('CSRF state token does not match one provided.');
				return false;
			}
		}
		
		return false;
	}

	/**
	 * Lays down a CSRF state token for this process.
	 *
	 * @return void
	 */
	protected function establishCSRFTokenState()
	{
		if ($this->state === null) {
			$this->state = md5(uniqid(mt_rand(), true));
			$this->store->set('state', $this->state);
		}
	}
	
	protected function setError($errcode, $errmsg)
	{
		$this->errcode = $errcode;
		$this->errmsg = $errmsg;
	}
	
	private function shouldRetainParam($param)
	{
		foreach (self::$DROP_QUERY_PARAMS as $drop_query_param) {
			if (strpos($param, $drop_query_param . '=') === 0) {
				return false;
			}
		}
		
		return true;
	}
	
	private function converJson2Array($json)
	{
		$result = json_decode($json, true);
		if (strcasecmp($this->finalEncode, 'UTF-8') !== 0) {
			$result = self::iconv($result, 'UTF-8', $this->finalEncode);
		}

		return $result;
	}

	private function convertXml2Array($xml)
	{
		$sxml = simplexml_load_string($xml);
		$result = self::convertSimpleXml2Array($sxml, $this->finalEncode);
		return $result;
	}

	private static function convertSimpleXml2Array($sxml, $finalEncode)
	{
		$arr = array();
		if ($sxml) {
			foreach ($sxml as $k => $v) {
				if ($sxml['list']) {
					$arr[] = self::convertSimpleXml2Array($v, $finalEncode);
				} else {
					$arr[$k] = self::convertSimpleXml2Array($v, $finalEncode);
				}
			}
		}
		
		if (count($arr) > 0) {
			return $arr;
		} else {
			if (strcasecmp($finalEncode, 'UTF-8') !== 0) {
				return iconv('UTF-8', $finalEncode, $sxml);
			} else {
				return (string)$sxml;
			}
		}
	}
}