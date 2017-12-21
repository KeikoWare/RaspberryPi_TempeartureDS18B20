<?
include("../dbconnect/dbconnect.php");
    function getRealIpAddr(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){  //check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){   //to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
$s = @$_REQUEST["sensor"];
$uid = @$_REQUEST["uid"];
$t = @$_REQUEST["temperature"];
$h = @$_REQUEST["humidity"];
$ip = getRealIpAddr();
$sql = "INSERT INTO temperature (sensor,temperature,humidity,RPIuniqueId,IPadresses) VALUES('$s','$t','$h','$uid','$ip');";
mysql_query($sql);
mysql_close();
echo "OK " . mysql_error();
?> 
