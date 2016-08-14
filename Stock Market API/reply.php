<html>

<?php
//PHP Code goes here.
if(isset($_GET['company']))
{
	$url="http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=".$_GET['symbol'];
	$response = file_get_contents($url);
	$response = json_decode($response,true);
}
?>
</html>