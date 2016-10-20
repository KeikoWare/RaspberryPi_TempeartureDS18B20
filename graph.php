<?php
include('../dbconnect/dbconnect.php');

$tempHigh = @$_REQUEST["temphigh"];
$tempLow = @$_REQUEST["templow"];
$tempScale = @$_REQUEST["tempscale"];

if($tempHigh == "") $tempHigh = 30;
if($tempLow == "") $tempLow = -10;
if($tempScale == "") $tempScale = 5;
$tempIntervals = (ceil($tempHigh/$tempScale) - floor($tempLow/$tempScale));

$startDate = @$_REQUEST["startdato"];
$startTime =  @$_REQUEST["tidspunkt"];
$timeScale = @$_REQUEST["skala"]; // year, month, week, day, hour 

if($timeScale == "") $timeScale = 'day';
if($startDate == "") $startDate = date('Y-m-d 00:00:00'); 
if($startTime != "" && $timeScale == 'hour') $startDate = $startDate . " " . $startTime;
$intStart = strtotime($startDate); 
$intervals =  ($timeScale == 'hour' ? 24 : 7); // number of scale intervals 

$dh = ($timeScale == 'hour' ? $intervals : 0);
$dd = ($timeScale == 'day' ? $intervals : 0);
$dw = ($timeScale == 'week' ? $intervals * 7 : 0);
$dm = ($timeScale == 'month' ? $intervals : 0);
$dy = ($timeScale == 'year' ? $intervals : 0);
$intEnd = mktime(date('H',$intStart)+$dh,date('i',$intStart),date('s',$intStart),date('m',$intStart)+$dm,date('d',$intStart)+$dd+$dw,date('Y',$intStart)+$dy); 

$axisWidth = 50;

$headerHeight = 45;
$leftMargin = 20;
$intervalWidth = ($timeScale == 'hour' ? 24 : 12*6);
$tempScaleHeight = 50;
$timelineWidth = ($intervals * $intervalWidth); // Time Line Width
$graphHeight = $tempIntervals * $tempScaleHeight;
$picWidth = $timelineWidth + $axisWidth + $leftMargin * 3;
$picHeight = $graphHeight + $headerHeight + 3 *  $axisWidth ;

// Create a image
$im = imagecreatetruecolor($picWidth , $picHeight); // 3 * 180 + 8 * 180 = 540 + 8 * 180
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$red = imagecolorallocate($im, 255, 0, 0);
$green = imagecolorallocate($im,123,170,32);
$darkYellow = imagecolorallocate($im,255,192,0);
$darkRed = imagecolorallocate($im,192,0,0); 
$lightBlue = imagecolorallocate($im,232,238,247);  // background 
$lightBlueShadow = imagecolorallocate($im,202,216,236); 
$duschGreen = imagecolorallocate($im,196,214,160); 
$duschYellow = imagecolorallocate($im,215,215,120);
$duschRed = imagecolorallocate($im,220,154,154); 

$innerLine = imagecolorallocate($im,167,190,224); 
$outerLine = imagecolorallocate($im,64,64,64); 


// Draw header
imagefilledrectangle($im, 0, 0, $picWidth , $picHeight, $lightBlue);
$font = 'font/arial.ttf';
$text = 'Temperature Chart'; 
imagettftext($im, 24, 0, $leftMargin -3, $headerHeight + 3, $lightBlueShadow, $font, $text);
imagettftext($im, 24, 0, $leftMargin, $headerHeight, $black, $font, $text);


// Draw scale

// Timeline scale
$fontSize = 10;
for($i = 0; $i < $intervals +1; $i++){
	$x = $leftMargin + $axisWidth + $i * $intervalWidth;
	$y = $headerHeight + $axisWidth;
	imageline($im,$x,$y - 10,$x,$y+$graphHeight,$innerLine);

	$intDateTime = mktime(date('H',$intStart)+$i*$dh/$intervals,date('i',$intStart),date('s',$intStart),date('m',$intStart)+$i*$dm/$intervals,date('d',$intStart)+$i*$dd/$intervals+$i*$dw/$intervals,date('Y',$intStart)+$i*$dy/$intervals); 

	$text = date("d-m-Y",$intDateTime);
	$dimensions = imagettfbbox($fontSize, 45, $font, $text);
	$textWidth = abs($dimensions[4] - $dimensions[0]);
	if($timeScale == 'hour' AND $i == 0) imagettftext($im, $fontSize, 0, $x - $textWidth - 20 , $y+$graphHeight + abs($dimensions[5] - $dimensions[1]) + 20, $outerLine, $font, $text );
	elseif($timeScale == 'hour' AND $i > 0 AND date("H:i",$intDateTime) == '00:00') imagettftext($im, $fontSize, 0, $x - $textWidth - 20 , $y+$graphHeight + abs($dimensions[5] - $dimensions[1]) + 20, $outerLine, $font, $text );
	elseif($timeScale != 'hour') imagettftext($im, $fontSize, 45, $x - $textWidth, $y+$graphHeight + abs($dimensions[5] - $dimensions[1]) + 5, $outerLine, $font, $text );
	$text = date("H:i",$intDateTime); 
	if($timeScale == 'hour') imagettftext($im, $fontSize, 45, $x - $textWidth + $fontSize * 2 - 3, $y+$graphHeight + abs($dimensions[5] - $dimensions[1]) , $outerLine, $font, $text );
}


// Temperature scale
$fontSize = 12;
$maxTemp = ceil($tempHigh/$tempScale) * $tempScale;
$curTemp = $maxTemp;
for($i = 0; $i < $tempIntervals +1; $i++){
	$x = $leftMargin + $axisWidth; 
	$y = $headerHeight + $axisWidth + $i * $tempScaleHeight;
	imageline($im,$x,$y,$x + $timelineWidth + 10,$y,$innerLine);
	$text = $curTemp . json_decode('"\u00B0"');
	$dimensions = imagettfbbox($fontSize, 0, $font, $text); 
	$textWidth = abs($dimensions[4] - $dimensions[0]) + 10;	
	imagettftext($im, $fontSize, 0, $x - $textWidth, $y + floor($fontSize/2), $outerLine, $font, $text );
	$curTemp -= $tempScale;
} 
$minTemp = $curTemp;

$x = $leftMargin + $axisWidth;
$y = $headerHeight + $axisWidth;
imageline($im,$x,$y-10,$x,$y+$graphHeight,$outerLine);
$x = $leftMargin + $axisWidth;
$y = $headerHeight + $axisWidth+$graphHeight;
imageline($im,$x,$y,$x+$timelineWidth+10,$y,$outerLine);

// DATA
$sql = "SELECT * 
FROM  `temperature` 
WHERE  `temperature` >= " . $minTemp . "
AND  `temperature` <= " . $maxTemp . "
AND  `update` 
BETWEEN  '" . date("Y-m-d H:i:s",$intStart) . "'
AND  '" . date("Y-m-d H:i:s",$intEnd) . "' ";
$rs = mysql_query($sql);
$j = 0;
$newTValue = 0;
$newHValue = 0;
$newDateTime = 0;
$oldTValue = 0;
$oldHValue = 0;
$oldDateTime = 0;
$ax = $ay = 0;
$bx = $by = 0;
while($data = mysql_fetch_array($rs)){
	$newTValue = $data["temperature"];
	$newHValue  = $data["temperature"];
	$newDateTime = $data["update"];
	$ax = $leftMargin + $axisWidth + floor($timelineWidth * (strtotime($newDateTime) - $intStart) / ($intEnd - $intStart));
	$ay = $headerHeight + $axisWidth + $graphHeight  -  floor( $graphHeight * (($newTValue - $tempLow) / ($tempHigh - $tempLow)));
	if($j> 0){
		imageline ($im,$ax,$ay,$bx,$by,$green);
	}
	imagesetpixel ( $im , $ax , $ay+1 , $green );
	imagesetpixel ( $im , $ax , $ay-1 , $green );
	$bx = $ax; $by = $ay;
	$oldTValue = $newTValue;
	$oldHValue = $newHValue;
	$oldDateTIme = $NewDateTime;
	$j++;
}
// Save the image
// header('Content-Disposition: attachment; filename="temperaturegraph.png"');
header('Content-Disposition: inline; filename="temperaturegraph.png"');

header("Content-type: image/png"); 
imagepng($im); 
imagedestroy($im);
mysql_close();
?>