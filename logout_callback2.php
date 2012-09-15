<?php


require 'Baidu.php';

$baidu = new Baidu('A364CFOoZtNnrsRlusRbHK5r', '6GP1XYB9d8fqY8HY0uGCONiZljvmQfGA',
	                new BaiduCookieStore('A364CFOoZtNnrsRlusRbHK5r'));

$baidu->setSession(null);

header("Location: http://channelchat.duapp.com/index.php");

?>