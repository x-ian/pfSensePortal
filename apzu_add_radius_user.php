<?php
	$ip=$_SERVER['REMOTE_ADDR'];
	$name=$_REQUEST['name'];
	$email=$_REQUEST['email'];
        echo "Trying to register" . $name . $email . $ip;
	echo exec("/home/pfSensePortal/add_user_to_radius.sh " . $ip . " " . $name . " " . $email);
?>
