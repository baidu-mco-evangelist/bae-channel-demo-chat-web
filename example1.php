<?php

require '../Baidu.php';

$baidu = new Baidu('llYR1Ba1cZ3uwEX4tcbO6QL5', 'obh5DcqzusMmhxrlh8tlGYjHYt5px5OS',
	new BaiduCookieStore('llYR1Ba1cZ3uwEX4tcbO6QL5'));
$baidu->useHttps();

// Get User ID and User Name
$user = $baidu->getLoggedInUser();
if ($user) {
	$user_profile = $baidu->api('passport/users/getInfo', array('fields' => 'userid,username,sex,birthday'));
	if ($user_profile === false) {
		//get user profile failed
		var_dump(var_export(array('errcode' => $baidu->errcode(), 'errmsg' => $baidu->errmsg()), true));
		$user = null;
	}
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $baidu->getLogoutUrl(array('next' => 'http://www.hanguofeng.com/bdapp/demo/logout_callback.php'));
} else {
  $loginUrl = $baidu->getLoginUrl(array('response_type' => 'code',
  										'display' => 'popup',
  										'redirect_uri' => 'http://www.hanguofeng.com/bdapp/demo/login_callback.php'));
}

?>
<!doctype html>
<html>
  <head>
  	<meta http-equiv="content-type" content="text/html;charset=utf-8">
    <title>php-sdk</title>
    <link rel="stylesheet" type="text/css" href="LightFace.css"></link>
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
  </head>
  <body>
    <h1>php-sdk</h1>

    <?php if ($user): ?>

      <a id="logoutfrombaidu" href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a id="loginwithbaidu" href="#">Login with Baidu</a>
      </div>
    <?php endif ?>

    <h3>Cookies:</h3>
    <pre><?php print_r($_COOKIE); ?></pre>

    <?php if ($user): ?>
      <h3>Your User Object:</h3>
      <pre><?php var_dump(var_export($user_profile, true)); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>
    <script type="text/javascript" src="mootools-1.3.js"></script> 
	<script type="text/javascript" src="LightFace.js"></script> 
	<script type="text/javascript" src="LightFace.IFrame.js"></script> 
    
    <script>
    <?php if (!$user): ?>
    document.id('loginwithbaidu').addEvent('click',function() {
    	//获得窗口的垂直位置
        var iTop = (window.screen.availHeight-30-320)/2;        
        //获得窗口的水平位置
        var iLeft = (window.screen.availWidth-10-560)/2;
        window.open('<?php echo $loginUrl; ?>', 'newwindow',
            'height=320, width=560, top=' + iTop + ', left=' + iLeft +
            ', toolbar=no, menubar=no, ' +
            'scrollbars=no, resizable=no, location=no, status=no');
    });
    <?php else: ?>
    document.id('logoutfrombaidu').addEvent('click', function() {
		document.getElementById('logout_form').submit();
		return false;
    });
    <?php endif ?>
    </script>
    
  </body>
</html>
