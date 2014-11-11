<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Usage Statistics (only accurate when accounting is active)
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
  
  function userdetailslink($mac, $name) {
	  return '<a href="http://172.16.1.3/daloradius/mng-edit.php?username=' . $mac . '">' . $name . '</a>';
  }
  
//  $today = '2014-10-29';
$today = date('Y-m-d', strtotime('-0 day'));
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  $daysago7 = date('Y-m-d', strtotime('-6 days'));
  $daysago30 = date('Y-m-d', strtotime('-29 days'));
  
  
echo "<table><tr><th>User statistics</th><th>Today ($today)</th><th>Yesterday ($yesterday)</th><th>Last 7 days (from $daysago7)</th><th>Last 30 days (from $daysago30)</th></tr>";
  // active
  function active($startday, $endday) {
	return 'select radusergroup.groupname as groupname, count(distinct(radacct.username)) as count from radacct left join  radusergroup ON radacct.username=radusergroup.username where  ((acctstarttime < date(date_add("' . $endday . '", INTERVAL +1 DAY)) and acctstoptime > "' . $startday . '") or (acctstarttime < date(date_add("' . $endday . '", INTERVAL +1 DAY)) and acctstoptime is null)) group by groupname;';
  }
echo "<tr>";
echo "<td>devices active</td>";
echo "<td>";
$active_today = query(active($today, $today));
while ($row = mysql_fetch_assoc($active_today)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$active_yesterday = query(active($yesterday, $yesterday));
while ($row = mysql_fetch_assoc($active_yesterday)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$active_7daysago = query(active($daysago7, $today));
while ($row = mysql_fetch_assoc($active_7daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$active_30daysago = query(active($daysago30, $today));
while ($row = mysql_fetch_assoc($active_30daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "</tr>";
mysql_free_result($active_today);
mysql_free_result($active_yesterday);
mysql_free_result($active_7daysago);
mysql_free_result($active_30daysago);



// denied access
echo "<tr>";
echo "<td>todo: devices denied access</td>";
echo "</tr>";


  // newly registered
  function registered($startday, $endday) {
	return 'SELECT radusergroup.groupname as groupname, count(distinct(radcheck.username)) as count FROM radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username LEFT JOIN userinfo ON radcheck.username=userinfo.username where creationdate > "' . $startday . '" and creationdate <  date(date_add("' . $endday . '", INTERVAL +1 DAY)) GROUP by groupname;';
  }
echo "<tr>";
echo "<td>devices registered</td>";
echo "<td>";
$registered_today = query(registered($today, $today));
while ($row = mysql_fetch_assoc($registered_today)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$registered_yesterday = query(registered($yesterday, $yesterday));
while ($row = mysql_fetch_assoc($registered_yesterday)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$registered_7daysago = query(registered($daysago7, $today));
while ($row = mysql_fetch_assoc($registered_7daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$registered_30daysago = query(registered($daysago30, $today));
while ($row = mysql_fetch_assoc($registered_30daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "</tr>";
mysql_free_result($registered_today);
mysql_free_result($registered_yesterday);
mysql_free_result($registered_7daysago);
mysql_free_result($registered_30daysago);


// ever registered as of ...
  function ever($endday) {
	return 'SELECT radusergroup.groupname as groupname, count(distinct(radcheck.username)) as count FROM radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username LEFT JOIN userinfo ON radcheck.username=userinfo.username where creationdate < date_add("' . $endday . '", INTERVAL +1 DAY) GROUP by radusergroup.groupname order by groupname;';
  }
echo "<tr>";
echo "<td>devices ever reg.</td>";
echo "<td>";
$ever_today = query(ever($today));
while ($row = mysql_fetch_assoc($ever_today)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$ever_yesterday = query(ever($yesterday));
while ($row = mysql_fetch_assoc($ever_yesterday)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$ever_7daysago = query(ever($daysago7));
while ($row = mysql_fetch_assoc($ever_7daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "<td>";
$ever_30daysago = query(ever($daysago30));
while ($row = mysql_fetch_assoc($ever_30daysago)) {
    echo $row['count'] . ' (' . $row['groupname'] . ')<br/>';
}
echo "</td>";
echo "</tr>";
mysql_free_result($ever_today);
mysql_free_result($ever_yesterday);
mysql_free_result($ever_7daysago);
mysql_free_result($ever_30daysago);

  
  // download
  function top_download($startday, $endday, $topX) {
return 'SELECT distinct(radacct.UserName) as username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((sum(radacct.AcctOutputOctets)/1000000)) as download FROM radacct     LEFT JOIN radusergroup ON radacct.username=radusergroup.username LEFT JOIN userinfo ON radacct.username=userinfo.username    WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) group by UserName order by download desc limit ' . $topX . ';';  
  }
$down_today = query(top_download($today, $today, 10));
$down_yesterday = query(top_download($yesterday, $yesterday, 10));
$down_last7days = query(top_download($daysago7, $today, 10));
$down_last30days = query(top_download($daysago30, $today, 10));

  function total_download($startday, $endday) {
return 'SELECT ROUND((sum(radacct.AcctOutputOctets)/1000000)) as download FROM radacct WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) ;';  
  }
$down_total_today = query(total_download($today, $today));
$down_total_yesterday = query(total_download($yesterday, $yesterday));
$down_total_last7days = query(total_download($daysago7, $today));
$down_total_last30days = query(total_download($daysago30, $today));
echo "<table><tr><th>Download (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($down_total_today)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_total_yesterday)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_total_last7days)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_total_last30days)) {
	echo $row['download'];
}	
echo '</td></tr>';
mysql_free_result($down_total_today);
mysql_free_result($down_total_yesterday);
mysql_free_result($down_total_last7days);
mysql_free_result($down_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_today)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_yesterday)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_last7days)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_last30days)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($down_today);
mysql_free_result($down_yesterday);
mysql_free_result($down_last7days);
mysql_free_result($down_last30days);


// upload
  function top_upload($startday, $endday, $topX) {
return 'SELECT distinct(radacct.UserName) as username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((sum(radacct.AcctInputOctets)/1000000)) as upload FROM radacct     LEFT JOIN radusergroup ON radacct.username=radusergroup.username LEFT JOIN userinfo ON radacct.username=userinfo.username    WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) group by UserName order by upload desc limit ' . $topX . ';';  
  }
$up_today = query(top_upload($today, $today, 10));
$up_yesterday = query(top_upload($yesterday, $yesterday, 10));
$up_last7days = query(top_upload($daysago7, $today, 10));
$up_last30days = query(top_upload($daysago30, $today, 10));
echo "<table><tr><th>Upload (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";

  function total_upload($startday, $endday) {
return 'SELECT ROUND((sum(radacct.AcctInputOctets)/1000000)) as upload FROM radacct WHERE (AcctStopTime > "0000-00-00 00:00:01" AND AcctStartTime>"' . $startday . '" AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) OR ((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = "0000-00-00 00:00:00") AND AcctStartTime<date(date_add("' . $endday . '", INTERVAL +1 DAY))) ;';  
  }
$up_total_today = query(total_upload($today, $today));
$up_total_yesterday = query(total_upload($yesterday, $yesterday));
$up_total_last7days = query(total_upload($daysago7, $today));
$up_total_last30days = query(total_upload($daysago30, $today));
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($up_total_today)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_total_yesterday)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_total_last7days)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_total_last30days)) {
	echo $row['upload'];
}	
echo '</td></tr>';
mysql_free_result($up_total_today);
mysql_free_result($up_total_yesterday);
mysql_free_result($up_total_last7days);
mysql_free_result($up_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_today)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_yesterday)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_last7days)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_last30days)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($up_today);
mysql_free_result($up_yesterday);
mysql_free_result($up_last7days);
mysql_free_result($up_last30days);


// breakdown
/*echo "<table><tr><th>Download (MB)</th><th>Name</th><th>Email</th><th>MAC Address</th><th>Group</th><th>Organization</th><th>Hostname</th><th>Vendor</th></tr>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<tr>";
    echo "<td>" . $row['download'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . $row['groupname'] . "</td>";
    echo "<td>" . $row['company'] . "</td>";
    echo "<td>" . $row['address'] . "</td>";
    echo "<td>" . $row['city'] . "</td>";
	echo "</tr>";
}
echo "</table>";
*/
/*
select groupname, 
  (select value from radgroupcheck r2 where attribute="Lucent-Max-Shared-Users" and r2.groupname = r1.groupname)  "Max Concurrent Users", (select (value/1000000) from radgroupcheck r5 where attribute="CS-Output-Octets-Daily" and r5.groupname = r1.groupname)  "Max Daily Down", (select (value/1000000) from radgroupcheck r6 where attribute="CS-Input-Octets-Daily" and r6.groupname = r1.groupname)  "Max Daily Up", (select (value/1000000) from radgroupcheck r7 where attribute="CS-Output-Octets-Weekly" and r7.groupname = r1.groupname)  "Max Weekly Down", (select (value/1000000) from radgroupcheck r8 where attribute="CS-Input-Octets-Weekly" and r8.groupname = r1.groupname)  "Max Weekly Up" from radgroupcheck r1 group by groupname;

select groupname, 
  (select value from radgroupreply r3 where attribute='WISPr-Bandwidth-Max-Up' and r3.groupname = r1.groupname)  'Max Bandwidth Up',
  (select value from radgroupreply r4 where attribute='WISPr-Bandwidth-Max-Down' and r4.groupname = r1.groupname)  'Max Bandwidth Down', 
  ((select (value/1000000) from radgroupreply r5 where attribute='Session-Timeout' and r5.groupname = r1.groupname) * 1000000 / 3600)  'Session Timeout (h)'
from radgroupreply r1 group by groupname;

*/

$result = mysql_query('select groupname, (select value from radgroupcheck r2 where attribute="Lucent-Max-Shared-Users" and r2.groupname = r1.groupname)  "Max Concurrent Users", (select (value/1000000) from radgroupcheck r5 where attribute="CS-Output-Octets-Daily" and r5.groupname = r1.groupname)  "Max Daily Down", (select (value/1000000) from radgroupcheck r6 where attribute="CS-Input-Octets-Daily" and r6.groupname = r1.groupname)  "Max Daily Up", (select (value/1000000) from radgroupcheck r7 where attribute="CS-Output-Octets-Weekly" and r7.groupname = r1.groupname)  "Max Weekly Down", (select (value/1000000) from radgroupcheck r8 where attribute="CS-Input-Octets-Weekly" and r8.groupname = r1.groupname)  "Max Weekly Up" from radgroupcheck r1 group by groupname;');
  
$group_settings = mysql_fetch_assoc($result);
echo "<pre>";
print_r($group_settings);
echo "</pre>";
echo "<pre>";
var_dump($group_settings);
echo "</pre>";

$group_settings = mysql_fetch_row($result);
echo "<pre>";
print_r($group_settings);
echo "</pre>";


?>