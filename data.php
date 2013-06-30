<?php
/**
 * This file loads content from data table depending on the required time range.  
 * @param callback {String} The name of the JSONP callback to pad the JSON within
 * @param start {Integer} The starting point in JS time
 * @param end {Integer} The ending point in JS time
 */

require_once('config.inc.php');

// get the parameters

$callback = $_GET['callback'];
if (!preg_match('/^[a-zA-Z0-9_]+$/', $callback)) {
	die('Invalid callback name');
}

$sensor_id = isset($_GET['sensor'])? $_GET['sensor'] : null;
if (!$sensor_id || !preg_match('/^[0-9]+$/', $sensor_id)) {
	die("Invalid sensor parameter: $sensor_id");
}

$start = isset($_GET['start'])? $_GET['start'] : null;
if ($start && !preg_match('/^[0-9]+$/', $start)) {
	die("Invalid start parameter: $start");
}

$end = isset($_GET['end']) ? $_GET['end'] : null;
if ($end && !preg_match('/^[0-9]+$/', $end)) {
	die("Invalid end parameter: $end");
}
if (!$end) {
	$end = time() * 1000;
}

mysql_connect(DBHOST, DBUSER, DBPASSWORD) or die('die: mysql_connect');
mysql_select_db(DBDATABASE) or die('die: mysql_select_db');

// set UTC time
//mysql_query("SET time_zone = '+00:00'");

// set some utility variables
$range = $end - $start;
$startTime = strftime('%Y-%m-%d %H:%M:%S', $start / 1000);
//$endTime = gmstrftime('%Y-%m-%d %H:%M:%S', $end / 1000);
$endTime = strftime('%Y-%m-%d %H:%M:%S', $end / 1000);

if( $range < 3 * 3600 * 1000 )
{
	$group_by="time";
}
elseif($range < 10 * 3600 * 1000)
{
	$group_by = " ROUND( UNIX_TIMESTAMP( TIME ) / ( 5 * 60 ) ) ";
}
// one day range loads 10 min data
elseif ($range < 2 * 24 * 3600 * 1000) {
	//$group_by = "time";
	$group_by = " ROUND( UNIX_TIMESTAMP( TIME ) / ( 10 * 60 ) ) ";
// one month range loads hourly data
} elseif ($range < 31 * 24 * 3600 * 1000) {
	#$group_by = "DATE_FORMAT(time , '%Y-%m-%d %H')";
	$group_by = " ROUND( UNIX_TIMESTAMP( time ) / ( 24 * 60 * 60 ) )";

// one year range loads daily data
} elseif ($range < 15 * 31 * 24 * 3600 * 1000) {
	//$group_by = "DATE_FORMAT(time , '%Y-%m-%d')";
	$group_by = " ROUND( UNIX_TIMESTAMP( time ) / ( 31 * 24 * 60 * 60 ) )";

// greater range loads monthly data
} else {
	//$group_by = "DATE_FORMAT(time , '%Y-%m')";
	$group_by = "( YEAR(time) * 100 + MONTH(time) )";
} 


$sql = "

		select 
			min(unix_timestamp(time) * 1000) as datetime,
			avg(data) as data
		from data 
		where time between '$startTime' and '$endTime' 
			and sensor_id = $sensor_id
		group by $group_by
		order by time
		limit 0,5000


";

$result = mysql_query($sql) or die(mysql_error());


$rows = array();
while ($row = mysql_fetch_assoc($result)) {
	extract($row);
	
	$rows[] = "[$datetime,$data]";
}

// print it
header('Content-Type: text/javascript');

echo "/* console.log(' start = $start, end = $end, startTime = $startTime, endTime = $endTime '); */";
echo $callback ."([\n" . join(",\n", $rows) ."\n]);";


?>




















