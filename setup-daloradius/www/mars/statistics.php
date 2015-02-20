<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Usage Statistics (only accurate when accounting is active)
	</p>
</span>

<hr/>
<p>Registered devices overview</p>

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
	  return '<a href="/daloradius/mng-edit.php?username=' . $mac . '">' . $name . '</a>';
  }
  
//  $today = '2014-10-29';
$today = date('Y-m-d', strtotime('-0 day'));
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  $daysago7 = date('Y-m-d', strtotime('-6 days'));
  $daysago30 = date('Y-m-d', strtotime('-29 days'));
  
  
echo "<table><tr><th></th><th>Today ($today)</th><th>Yesterday ($yesterday)</th><th>Last 7 days (from $daysago7)</th><th>Last 30 days (from $daysago30)</th></tr>";
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
echo "</table>";
mysql_free_result($ever_today);
mysql_free_result($ever_yesterday);
mysql_free_result($ever_7daysago);
mysql_free_result($ever_30daysago);

  
echo "<hr/><p>Top downloads during working hours (Mo-Fr 7:00 to 18:00)</p>";
	
  // download work
  function top_download_work($startday, $endday, $topX) {
return 'SELECT daily_accounting.username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as upload, ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as download FROM daily_accounting LEFT JOIN radusergroup ON daily_accounting.username=radusergroup.username LEFT JOIN userinfo ON daily_accounting.username=userinfo.username WHERE daily_accounting.day >= "' . $startday . '" AND daily_accounting.day <= "' . $endday . '" GROUP BY daily_accounting.username ORDER BY download DESC LIMIT ' . $topX . ';';  
  }
$down_work_today = query(top_download_work($today, $today, 10));
$down_work_yesterday = query(top_download_work($yesterday, $yesterday, 10));
$down_work_last7days = query(top_download_work($daysago7, $today, 10));
$down_work_last30days = query(top_download_work($daysago30, $today, 10));

// download work
  function total_download_work($startday, $endday) {
return 'SELECT ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as download FROM daily_accounting WHERE day >= "' . $startday . '" AND day <= "' . $endday . '";';  
  }
$down_work_total_today = query(total_download_work($today, $today));
$down_work_total_yesterday = query(total_download_work($yesterday, $yesterday));
$down_work_total_last7days = query(total_download_work($daysago7, $today));
$down_work_total_last30days = query(total_download_work($daysago30, $today));
echo "<table><tr><th>Download (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($down_work_total_today)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_work_total_yesterday)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_work_total_last7days)) {
	echo $row['download'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($down_work_total_last30days)) {
	echo $row['download'];
}	
echo '</td></tr>';
mysql_free_result($down_work_total_today);
mysql_free_result($down_work_total_yesterday);
mysql_free_result($down_work_total_last7days);
mysql_free_result($down_work_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_today)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_yesterday)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_last7days)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($down_work_last30days)) {
	    echo $row['download'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($down_work_today);
mysql_free_result($down_work_yesterday);
mysql_free_result($down_work_last7days);
mysql_free_result($down_work_last30days);


echo "<hr/><p>Top uploads during working hours (Mo-Fr 7:00 to 18:00)</p>";
	
  // upload work
  function top_upload_work($startday, $endday, $topX) {
return 'SELECT daily_accounting.username, radusergroup.groupname as groupname, userinfo.lastname as name, userinfo.email as email, userinfo.company as company, userinfo.address as address, userinfo.city as city, ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as upload, ROUND((SUM(outputoctets_work_end) - SUM(outputoctets_work_beg)) / 1000000) as download FROM daily_accounting LEFT JOIN radusergroup ON daily_accounting.username=radusergroup.username LEFT JOIN userinfo ON daily_accounting.username=userinfo.username WHERE daily_accounting.day >= "' . $startday . '" AND daily_accounting.day <= "' . $endday . '" GROUP BY daily_accounting.username ORDER BY upload DESC LIMIT ' . $topX . ';';  
  }
$up_work_today = query(top_upload_work($today, $today, 10));
$up_work_yesterday = query(top_upload_work($yesterday, $yesterday, 10));
$up_work_last7days = query(top_upload_work($daysago7, $today, 10));
$up_work_last30days = query(top_upload_work($daysago30, $today, 10));

// upload work
  function total_upload_work($startday, $endday) {
return 'SELECT ROUND((SUM(inputoctets_work_end) - SUM(inputoctets_work_beg)) / 1000000) as upload FROM daily_accounting WHERE day >= "' . $startday . '" AND day <= "' . $endday . '";';  
  }
$up_work_total_today = query(total_upload_work($today, $today));
$up_work_total_yesterday = query(total_upload_work($yesterday, $yesterday));
$up_work_total_last7days = query(total_upload_work($daysago7, $today));
$up_work_total_last30days = query(total_upload_work($daysago30, $today));
echo "<table><tr><th>Upload (MB)</th><th>Today</th><th>Yesterday</th><th>Last 7 days</th><th>Last 30 days</th></tr>";
echo '<tr><td>Total</td><td>';
if ($row = mysql_fetch_assoc($up_work_total_today)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_work_total_yesterday)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_work_total_last7days)) {
	echo $row['upload'];
}	
echo '</td>';
echo '<td>';
if ($row = mysql_fetch_assoc($up_work_total_last30days)) {
	echo $row['upload'];
}	
echo '</td></tr>';
mysql_free_result($up_work_total_today);
mysql_free_result($up_work_total_yesterday);
mysql_free_result($up_work_total_last7days);
mysql_free_result($up_work_total_last30days);

for ($i=1; $i<=10; $i++) {
	echo "<tr>";
	echo "<td>Top #" . $i . "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_today)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']). " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_yesterday)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_last7days)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "<td>";
	if ($row = mysql_fetch_assoc($up_work_last30days)) {
	    echo $row['upload'] . " (" . userdetailslink($row['username'], $row['name']) . " " . $row['email'] . " " . $row['username'] . " " . $row['groupname'] . " " . $row['company'] . " " . $row['address'] . " " . $row['city'] . ")";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_free_result($up_work_today);
mysql_free_result($up_work_yesterday);
mysql_free_result($up_work_last7days);
mysql_free_result($up_work_last30days);


echo "<hr/><p>Top downloads total</p>";

  // download total
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


echo "<hr/><p>Top uploads total</p>";

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

echo "<hr/><p>Current group settings</p>";

$result = mysql_query('select rr1.groupname, (select value from radgroupcheck r2 where attribute="Lucent-Max-Shared-Users" and r2.groupname = r1.groupname)  "Max Concurrent Users", (select (value/1000000) from radgroupcheck r5 where attribute="CS-Output-Octets-Daily" and r5.groupname = r1.groupname)  "Max Daily Down", (select (value/1000000) from radgroupcheck r6 where attribute="CS-Input-Octets-Daily" and r6.groupname = r1.groupname)  "Max Daily Up", (select (value/1000000) from radgroupcheck r7 where attribute="CS-Output-Octets-Weekly" and r7.groupname = r1.groupname)  "Max Weekly Down", (select (value/1000000) from radgroupcheck r8 where attribute="CS-Input-Octets-Weekly" and r8.groupname = r1.groupname)  "Max Weekly Up", (select value from radgroupcheck r9 where attribute="xian-Output-Megabytes-Daily-Work-Hours" and r9.groupname = r1.groupname)  "Max Business Hours Down", (select value from radgroupcheck r10 where attribute="xian-Input-Megabytes-Daily-Work-Hours" and r10.groupname = r1.groupname)  "Max Business Hours Up", (select value from radgroupreply rr2 where attribute ="Session-Timeout" and rr2.groupname = rr1.groupname) "Session Timeout", (select value from radgroupreply rr3 where attribute ="WISPr-Bandwidth-Max-Up" and rr3.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Up", (select value from radgroupreply rr4 where attribute ="WISPr-Bandwidth-Max-Down" and rr4.groupname = rr1.groupname) "WISPr-Bandwidth-Max-Down" from radgroupreply rr1 left join radgroupcheck r1 on rr1.groupname = r1.groupname group by rr1.groupname;');  
  
echo "<table><tr><th>Group</th><th>Working Hours Up</th><th>Working Hours Down</th><th>Bandwidth Up</th><th>Bandwidth Down</th><th>Session Timeout</th><th>Concurrent Users</th><th>Daily Up</th><th>Daily Down</th><th>Weekly Up</th><th>Weekly Down</th></tr>";
while ($row = mysql_fetch_assoc($result)) {
	echo "<tr>";
	echo "<td>" . $row['groupname'] . "</td>";
	echo "<td>" . $row['Max Business Hours Up'] . "</td>";
	echo "<td>" . $row['Max Business Hours Down'] . "</td>";
	echo "<td>" . $row['WISPr-Bandwidth-Max-Up'] . "</td>";
	echo "<td>" . $row['WISPr-Bandwidth-Max-Down'] . "</td>";
	echo "<td>" . $row['Session Timeout'] . "</td>";
	echo "<td>" . $row['Max Concurrent Users'] . "</td>";
	echo "<td>" . $row['Max Daily Up'] . "</td>";
	echo "<td>" . $row['Max Daily Down'] . "</td>";
	echo "<td>" . $row['Max Weekly Up'] . "</td>";
	echo "<td>" . $row['Max Weekly Down'] . "</td>";
	echo "</tr>";
}
echo "</table>";
?>

<pre>
Up- and Download volumes in Megabytes
Bandwidth limits in bits/second
Session Timeout in s

25000 bits/second = 11.25 megabytes/hour
50000 bits/second = 22.5 megabytes/hour
150000 bits/second = 67.5 megabytes/hour
400000 bits/second = 180 megabytes/hour
2000000 bits/second = 900 megabytes/hour

with 1 megabyte = 1000000 bytes
</pre>