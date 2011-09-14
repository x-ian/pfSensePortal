<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Abwenzi Pa Za Umoyo / Partners In Health
	</p>
</span>
<table>
	<tr>
		<td>
			<img src="apzu.png" />
		</td>
		<td>
			Everybody gets connected - Hopefully you as well!<br/>
			<p><?php
				$ip=$_SERVER['REMOTE_ADDR'];
				$name=$_REQUEST['name'];
				$email=$_REQUEST['email'];
			    echo "The registration process should be done by now for " . $name . " (" . $email . ") from " . $ip;
				echo exec("/home/pfSensePortal/add_user_to_radius.sh " . $ip . " " . $name . " " . $email);
			?>
			</p>
			<p>If you are not able to access any webpages like <a href="http://www.google.mw">Google</a>, please find the APZU-IT team.</p>
		</td>
	</tr>
</table>

