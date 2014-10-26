
<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Abwenzi Pa Za Umoyo / Partners In Health
	</p>
</span>
<p align="center">Welcome to the network services provided by APZU/PIH.</p>
<table>
	<tr>
		<td>
			<img src="/captiveportal-apzu.jpg" />
		</td>
		<td>
				<?php
					$ip=$_SERVER['REMOTE_ADDR'];
					echo "<p>Please wait while your device is identified... ";
					echo exec("/home/pfSensePortal/daloradius-integration/captive-portal-check_device_status.sh " . $ip, $output, $exitCode);
								
					echo "</td></tr><tr><td>";
					if ($exitCode == 0) {
						// device enabled and no restrictions apply. should not happen as the captive portal should have automattically logged it in before this check
					}  
					if ($exitCode == 1) {
						// device disabled
						echo "</td><td><p><b>Your device is disabled. Please see the IT team for further explanation.</b></p>";
						echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . "</p></td>";
					}  
					if ($exitCode == 2) {
						// not yet registered
						include '/usr/local/captiveportal/captiveportal-device_registration.html';
					}
					  if ($exitCode == 3) {
						// access denied with additional restrictions
						echo "</td><td><p><b>Your device has used up your available data volume. Either check back tomorrow or next week.</b></p>";
						echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . "</p></td>";
						//} else {
						// unknwon return status
						//echo "<p>Error in Captive Portal. Please see the IT team.</p>"
					}
				?>
		</td>
	</tr>
</table>
