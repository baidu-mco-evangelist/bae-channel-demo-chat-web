<?php
// When user finish authorization, request will redirect to this page,
// then all we need to do is to get access token by authorization code,
// and then notify its parent page

require 'Baidu.php';

$baidu = new Baidu('A364CFOoZtNnrsRlusRbHK5r', '6GP1XYB9d8fqY8HY0uGCONiZljvmQfGA',
	                new BaiduCookieStore('A364CFOoZtNnrsRlusRbHK5r'));

$access_token = $baidu->getAccessToken();
var_dump($access_token);

$user = $baidu->getLoggedInUser();

var_dump($user);
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
    <script>
    	parent.location.reload();
    </script>
  </body>
</html>