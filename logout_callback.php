<?php


require '../Baidu.php';

$baidu = new Baidu('llYR1Ba1cZ3uwEX4tcbO6QL5', 'obh5DcqzusMmhxrlh8tlGYjHYt5px5OS',
	new BaiduCookieStore('llYR1Ba1cZ3uwEX4tcbO6QL5'));

$baidu->setSession(null);

header("Location: http://www.hanguofeng.com/bdapp/demo/example1.php");

?>