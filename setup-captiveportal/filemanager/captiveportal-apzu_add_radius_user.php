<html>
<head>
<meta http-equiv="Refresh" content="3; url=http://www.google.mw/" />
</head>
<body>
<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Abwenzi Pa Za Umoyo / Partners In Health
	</p>
</span>
<table>
	<tr>
		<td>
			<img src="captiveportal-apzu.png" />
		</td>
		<td>
			Everybody gets connected - Hopefully you by now as well!<br/>
			<p><?php
				$ip=$_SERVER['REMOTE_ADDR'];
				$name=$_REQUEST['name'];
				$email=$_REQUEST['email'];
				$owner=$_REQUEST['owner'];
				$primary=$_REQUEST['primary'];
			    echo "The registration process should be done for " . $name . " (" . $email . ") from " . $ip;
				echo exec("/home/pfSensePortal/daloradius-integration/captive-portal-add_user_to_radius.sh " . $ip . " \"" . $name . "\" \"" . $email . "\" " . "\"" . $owner . "\" " . "\"" . $primary . "\"");
			?>
			</p>
			<p>If you are not able to access any webpages like <a href="http://www.google.mw">Google</a>, please try it <a href="/">again</a> in a few minutes.</p>
		</td>
	</tr>
</table>
</body>
</html>