<?php

function err($str)
{
	header("HTTP/1.0 404 Not Found");
	echo $str;
	die();
}

require_once('config.inc.php');

$sensor = isset($_GET['sensor'])? $_GET['sensor'] : null;
if (!$sensor || !preg_match('/^[0-9a-f-]+$/', $sensor)) {
	err(1);
}

$data = isset($_GET['data'])? $_GET['data'] : null;
if (!$data || !preg_match('/^[0-9\.]+$/', $data)) {
	err(2);
}

mysql_connect(DBHOST, DBUSER, DBPASSWORD) or err(3);
mysql_select_db(DBDATABASE) or err(4);

$sql="SELECT id from sensor where uuid='$sensor' limit 0,1";
$result = mysql_query($sql);

if (!$result)	err(5);

$id=mysql_result($result, 0, 0);

$sql="
	INSERT INTO data (
	id,
	sensor_id,
	time ,
	data
	)
	VALUES (
	NULL ,  $id, 
	CURRENT_TIMESTAMP ,  $data
	)";
	
mysql_query("SET time_zone = '+00:00'");

mysql_query($sql) or err(6);


?>




















