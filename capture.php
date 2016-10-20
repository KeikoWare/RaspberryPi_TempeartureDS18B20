<?
include("../dbconnect/dbconnect.php");
$s = @$_REQUEST["sensor"];
$t = @$_REQUEST["temperature"];
$h = @$_REQUEST["humidity"];
$sql = "INSERT INTO temperature (sensor,temperature,humidity) VALUES('$s','$t','$h');";
mysql_query($sql);
mysql_close();
echo "OK [$s,$t,$h] " . mysql_error();
?> 