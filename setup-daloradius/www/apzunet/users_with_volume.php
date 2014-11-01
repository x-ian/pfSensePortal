<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Data volume for all registered devices <?php echo date('Y-m-d H:i:s'); ?>
	</p>
	<br/>
</span>

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
  
  function userdetailslink($mac) {
	  return '<a href="http://172.16.1.3/daloradius/mng-edit.php?username=' . $mac . '">' . $mac . '</a>';
  }
  
  $today = date('Y-m-d', strtotime('-0 day'));
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $daysago7 = date('Y-m-d', strtotime('-6 days'));
    $daysago30 = date('Y-m-d', strtotime('-29 days'));
  
echo "<table><tr><th>Username</th><th>Group</th><th>Name</th><th>Email</th><th>Organization</th><th>Computername</th><th>Vendor</th><th>Down last 7 days</th><th>Up last 7 days</th></tr>";

  function users($startday, $endday) {
return 'select * from ( (SELECT distinct(radacct.UserName) as username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((sum(radacct.AcctOutputOctets)/1000000)) as download, ROUND((sum(radacct.AcctInputOctets)/1000000)) as upload FROM radacct     LEFT JOIN radusergroup ON radacct.username=radusergroup.username LEFT OUTER JOIN userinfo ON radacct.username=userinfo.username    WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime> "' . $startday . '" AND AcctStartTime<date(date_add("' .$endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) group by UserName) union (select ui.username, "", "", "", "", "", "", "0" as download, "0" as upload from userinfo ui) ) as t1 group by username;';  
  }
  
$all_users = query(users($daysago7, $today));
while ($row = mysql_fetch_assoc($all_users)) {
	echo "<tr>";
    echo '<td>' . userdetailslink($row['username']) . '</a></td>';
    echo '<td>' . $row['groupname'] . '</td>';
    echo '<td>' . $row['name'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
    echo '<td>' . $row['company'] . '</td>';
    echo '<td>' . $row['address'] . '</td>';
    echo '<td>' . $row['city'] . '</td>';
    echo '<td>' . $row['download'] . '</td>';
    echo '<td>' . $row['upload'] . '</td>';
	echo "</tr>";
}
mysql_free_result($all_users);

?>

