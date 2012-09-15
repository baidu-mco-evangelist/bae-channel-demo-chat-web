<?php
// When user finish authorization, request will redirect to this page,
// then all we need to do is to get access token by authorization code,
// and then notify its parent page

require '../Baidu.php';

$baidu = new Baidu('llYR1Ba1cZ3uwEX4tcbO6QL5', 'obh5DcqzusMmhxrlh8tlGYjHYt5px5OS',
	new BaiduCookieStore('llYR1Ba1cZ3uwEX4tcbO6QL5'));

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
  	<div align="center">
  	<p>Access Token: <?php echo $baidu->getAccessToken();?></p>
  	<p>给你<span style="color:red" id="timeticket">3</span>秒钟的时间看看Access Token长啥样的O(∩_∩)O哈哈~</p>
  	</div>
  	
    <script>
		$t = 3;
		$tid = window.setInterval("callback()",1000);

    	function callback()
    	{
        	if ($t > 0) {
            	$t--;
            	document.getElementById("timeticket").innerHTML = $t;
        	} else {
        		window.clearInterval($tid);
    			//parent.location.reload();
        		window.opener.location.reload();
    			window.close();
        	}
    	}
    	
    </script>
  </body>
</html>