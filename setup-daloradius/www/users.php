<?php
  mysql_connect('localhost','radius','radius') or die('Could not connect to mysql server.');
  mysql_select_db('radius');

  function query($query) {
    $result = mysql_query($query);
	if (!$result) {
		$message  = 'Ungültige Abfrage: ' . mysql_error() . "\n";
		$message .= 'Gesamte Abfrage: ' . $query;
    	die($message);
	} 
	return $result;
  }
  
  
echo "<table><tr><th>Username</th><th>Group</th><th>Name</th><th>Email</th><th>Primary device</th><th>Organization</th><th>Computername</th><th>Vendor</th><th>Notes</th><th>Creation date</th></tr>";

  function users() {
	return 'SELECT distinct(radcheck.username) as username,radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.mobilephone as primarydev, userinfo.company as org, userinfo.address as hostname, userinfo.city as vendor, userinfo.notes as notes, userinfo.creationdate as creationdate FROM radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username LEFT JOIN userinfo ON radcheck.username=userinfo.username GROUP by radcheck.Username order by creationdate;';
  }
$all_users = query(users());
while ($row = mysql_fetch_assoc($all_users)) {
	echo "<tr>";
    echo '<td>' . $row['username'] . '</td>';
    echo '<td>' . $row['groupname'] . '</td>';
    echo '<td>' . $row['name'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
    echo '<td>' . $row['primarydev'] . '</td>';
    echo '<td>' . $row['org'] . '</td>';
    echo '<td>' . $row['hostname'] . '</td>';
    echo '<td>' . $row['vendor'] . '</td>';
    echo '<td>' . $row['notes'] . '</td>';
    echo '<td>' . $row['creationdate'] . '</td>';
	echo "</tr>";
}
mysql_free_result($all_users);

?>

