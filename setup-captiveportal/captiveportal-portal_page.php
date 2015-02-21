
<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		mars Portal
	</p>
</span>
<p align="center">Welcome to the network services powered by mars Portal.</p>
<table>
	<tr>
		<td>
			<img src="/captiveportal-mars.jpg" />
		</td>
		<td>
				<?php
					$ip=$_SERVER['REMOTE_ADDR'];
					echo "<p>Please wait while your device is identified... ";
					echo exec("/home/marsPortal/daloradius-integration/captive-portal-check_device_status.sh " . $ip, $output, $exitCode);
								
					echo "</td></tr><tr><td>";
					switch ($exitCode) {
						case 0:
							// device enabled and no restrictions apply. should not happen as the captive portal should have automattically logged it in before this check
							break;
						case 1:
							// not yet registered
							include '/usr/local/captiveportal/captiveportal-device_registration.html';
							break;
						case 2:
							// device disabled
							echo "</td><td><p><b>Too many users. Please try again later.</b></p>";
							echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p></td>";
							break;
						case 3:
							// access denied with additional restrictions
							echo "</td><td><p><b>Your device has used up your available data volume. Either check back tomorrow or next week.</b></p>";
							echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p></td>";
							break;
						case 4:
							// device disabled
							echo "</td><td><b>Your device is disabled. Please see the IT team for further explanation.</b>";
							echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p></td>";
							break;
						case 5:
							// data bundle during business hours exceeded
							echo "</td><td><p><b>Your device has reached the maximum daily data bundle during working hours (Monday to Friday from 7 am to 6 pm). Please try again tomorrow.</b></p>";
							echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p>";
							exec("/home/pfSensePortal/daloradius-integration/echo-user-data-statistics-link.sh " . $ip, $out, $exit);
							echo "In doubt, check your data usage of the last 7 days: " . implode(" ", $out) . "</td>";
							break;
						default:
							// unknown response or server down
							echo "</td><td><p><b>Network not available. Please see the IT team if this message remains for a few hours.</b></p>";
							echo "<p>Exit code: $exitCode - (Reason: " . implode(" ", $output) . ")</p></td>";
					}  
				?>
		</td>
	</tr>
</table>
