<?php $username = $_GET['username']; ?>

<span style="font-variant:small-caps; font-size:200%">
	<p align="center">
		Data volume for device <?php echo $username . ' at ' . date('Y-m-d H:i:s')?>
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
  
  $today = date('Y-m-d', strtotime('-0 day'));
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  $daysago7 = date('Y-m-d', strtotime('-6 days'));
  $daysago30 = date('Y-m-d', strtotime('-29 days'));
  
echo "<table><tr><th/><th>Business down</th><th>Business up</th><th>Non-Business down</th><th>Non-Business up</th><th>Total down</th><th>Total up</th></tr>";

  function user($username, $start) {
return 'select username, day, ROUND((IF(inputoctets_day_end = 0, inputoctets_work_end, inputoctets_day_end) - inputoctets_day_beg) / 1000000) as total_input, ROUND((IF(outputoctets_day_end = 0, outputoctets_work_end, outputoctets_day_end) - outputoctets_day_beg) / 1000000) as total_output, ROUND((inputoctets_work_end - inputoctets_work_beg) / 1000000) as work_input, ROUND((outputoctets_work_end - outputoctets_work_beg) / 1000000) as work_output, ROUND((inputoctets_work_beg - inputoctets_day_beg + IF(inputoctets_day_end = 0, 0, inputoctets_day_end - inputoctets_work_end)) / 1000000) as non_work_input, ROUND((outputoctets_work_beg - outputoctets_day_beg + IF(outputoctets_day_end = 0, 0, outputoctets_day_end - outputoctets_work_end)) / 1000000) as non_work_output from daily_accounting where username = "' . $username . '" and day > "' . $start . '" group by username, day order by day desc;';  
  }
    
$all_traffic = query(user($username, $daysago30));
while ($row = mysql_fetch_assoc($all_traffic)) {
	echo "<tr>";
	echo '<td>' . $row['day'] . '</td>';
    echo '<td>' . $row['work_output'] . '</td>';
    echo '<td>' . $row['work_input'] . '</td>';
    echo '<td>' . $row['non_work_output'] . '</td>';
    echo '<td>' . $row['non_work_input'] . '</td>';
    echo '<td>' . $row['total_output'] . '</td>';
    echo '<td>' . $row['total_input'] . '</td>';
	echo "</tr>";
}
mysql_free_result($all_traffic);
?>
</table>

<p>(Values for today only accurate after begin of working hours.)</p>