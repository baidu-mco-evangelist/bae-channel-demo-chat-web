<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />

		<script type="text/javascript" src="http://openapi.baidu.com/connect/js/v2.0/featureloader"></script>
		<script type="text/javascript" src="https://channel.api.duapp.com/jssdk/channel.min.js"></script>
		<script type="text/javascript" src="/demo/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/demo/channel.js"></script>
	</head> 
	
  	<body>
		<table width="1421" height="31" border="0">
  			<tr>
    			<td width="34">&nbsp;</td>
				<td width="219"></td>
				<td width="700">&nbsp;</td>
				<td width="181"><input id="login" value="login" type="button"></input></td>
				<td width="181"><input id="logout" value="logout" type="button" style="display:none"></input></td>
			</tr>
		</table>  

		<table width="1421" border="0">
	  		<tr>
    			<td width="150"><textarea name="textarea" cols="23" rows="45" id = 'showUser' readonly="readonly"></textarea></td>
				<td>   				
					<textarea cols="160" rows="35" readonly="readonly" id="showMessage"></textarea>
					<textarea cols="160" rows="6" id="putMessage"  onkeydown="javascript:if(event.keyCode==13)if(document.getElementById('putMessage').value) sendMessage();else alert('message is null');""></textarea>
				    <p>
						<input name="start" id="sendMessages" value="send" type="button"  onClick="sendMessage();">	
					</p>
	            </td>
			</tr>
	  
		</table> 
  	</body>
 <script>
	document.getElementById('putMessage').readOnly = true;
	document.getElementById('sendMessages').disabled = true;

</script> 
</html>