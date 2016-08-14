<html>
<head>
	<style>

		body{
			text-align:center;
		}
		table
		{
			text-align: left;
			background-color: #f2f2f2;
			 margin: 0 auto;
		  	width: 650px;
	    	border:1px solid #a6a6a6;
		}
		th {
			background-color: #e6e6e6;
			}
		img
		 {
			height: 15px;
			width: 15px;
			 vertical-align: middle;
		 }
		#heading{
			font-style:italic;
			font-size:250%;
			margin:-5px;
		}
		#main_table
		{
			margin:0 auto;
			width:500px;
		}
		#label
		{
			font-style: italic;
		}
		hr
		{
			width: 98%;
			color: #a6a6a6;
		}
		#jason_table td{
			height:30;
		}
		td{
			height:40;
		}
	</style>

	<title>Stock Market Simulation</title>
</head>
<body>
 <form method="POST" action="stock.php">
     	 <table id="main_table">
         <tr><td id="label" colspan="3" align="center"><font size="6" weight="900">Stock Search</font><hr></td></tr>
         <tr><td align="right">Company Name or Symbol:</td><td align:"left"><input id="text_input" autofocus type="text" style="width:180;" name="company" id="company" value="<?php if(isset($_REQUEST['company'])){echo $_REQUEST['company'];}?>" pattern="[ ]*[A-Za-z0-9]+[ ]*" required x-moz-errormessage="Please enter Valid Company Name or Symbol"></td></tr>
         <tr><td></td><td><input type="submit" name="submit" value="Search">     <input type="button" name="reset" value="Clear" onclick="clearfunction()"></td></tr>
         <tr><td colspan ="2" align="center"><a href="http://www.markit.com/product/markit-on-demand" target="_blank">Powered by Markit on Demand</a></td></tr>
         <tr height="20"></tr>
         </table>
         </form>
<?php
//PHP Code goes here.
if(isset($_POST['submit']))
{
	$name_variable=$_POST["company"];
	$name_variable=trim($name_variable," ");
	$url="http://dev.markitondemand.com/MODApis/Api/v2/Lookup/xml?input=".$name_variable;
	$response = file_get_contents($url);
	$response = new SimpleXMLElement($response);
	echo "<table border='1' style='border-collapse: collapse'>";
	if(!$response)
		echo "<tr><td align='center'>No Records have been found</td></tr>";
	else
	{
		echo "<tr><th>Name</th><th>Symbol</th><th>Exchange</th><th>Details</th>\n";
		foreach($response->children() as $LookupResult)
		{
			$symbol=$LookupResult->Symbol;
			$jasonurl="http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=".$LookupResult->Symbol;
		   echo "<tr><td>".$LookupResult->Name."</td><td>".$LookupResult->Symbol."</td><td>".$LookupResult->Exchange."</td><td>"."<a href='?symbol=".$LookupResult->Symbol."&company=".$name_variable."'>More&nbspinfo</a></td>\n";
		}
		echo "</table>";
	}
}
else if(isset($_GET['symbol']))
{
	$stop=false;
	$url="http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=".$_GET['symbol'];
	$response = file_get_contents($url);
	$response = json_decode($response,true);
	date_default_timezone_set('America/Los_Angeles');
	echo "<div id='jason_table'><table align='center' border='1' style='border-collapse: collapse'>";
	if(!$response)
	{
		echo "<tr><td align='center'>No Stock Information have been found</td></tr>";
	}
	else
	{
		foreach($response as $key => $value)
		{
			if($stop === true)
				break;
			switch ($key)
			{

				case 'Message':
					echo "<tr><td align='center'>No Stock Information have been found</td></tr>";
					break;

				case 'Name':
					echo "<tr><td ><b>".$key."<b></td><td align='center'>".$value."</td></tr>";
					 break;

				case 'Symbol':
					 echo "<tr><td ><b>".$key."<b></td><td align='center'>".$value."</td></tr>";
					 break;

				case 'LastPrice':
 					$lp = $value;
					 echo "<tr><td ><b>"."Last Price"."<b></td><td align='center'>".$lp."</td></tr>";
					 break;

				case 'Change':
					 echo "<tr><td ><b>".$key."<b></td>";
					if($value==0)
						echo "<td align='center'>0</td>";
					else if($value > 0)
					 	echo "<td align='center'>".round($value, 2)." <img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png'></td>";
					 else
					 	echo "<td align='center'>".round($value, 2)." <img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png'></td>";
					 echo "</tr>";
					 break;

				case 'ChangePercent':
					 echo "<tr><td ><b>"."Change Percent"."<b></td>";
					if($value==0)
						echo "<td align='center'>0</td>";
					else if($value > 0)
					 	echo "<td align='center'>".round($value, 2)."%"."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png'></td>";
					 else
						echo "<td align='center'>".round($value, 2)."%"."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png'></td>";
					 echo "</tr>";
					 break;

				case 'Timestamp':
					$dval=strtotime($value);
					$display=date("Y-m-d h:i A",$dval);
					echo "<tr><td ><b>".$key."<b></td><td align='center'>".$display."</td></tr>";
					break;

				case 'MarketCap':
					  echo "<tr><td ><b>"."Market Cap"."<b></td>";
					  $market = ($value/1000000000);
					  $millions = ($market * 1000);
					  if($millions < 10)
					  	echo "<td align='center'>".round($millions, 2)." M</td>";
					  else
				 	    echo "<td align='center'>".round($market, 2)." B</td>";
					  echo "</tr>";
					  break;

				case 'Volume':
					 echo "<tr><td ><b>".$key."<b></td><td align='center'>".number_format($value, 0,'',',')."</td></tr>";
					 break;

				case 'ChangeYTD':
						 echo "<tr><td ><b>"."Change YTD"."<b></td>";
					    $change = $lp - $value ;
						if($change==0)
							echo "<td align='center'>0</td>";
						else if($change < 0)
						 	echo "<td align='center'>(".round($change, 2).") <img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png'></td>";
						 else
							echo "<td align='center'>".round($change, 2)." <img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png'></td>";

						 echo "</tr>";
						 break;

				case 'ChangePercentYTD':
					 echo "<tr><td ><b>"."Change Percent YTD"."<b></td>";
					if($value==0)
						echo "<td align='center'>0</td>";
					else if($value > 0)
					 	echo "<td align='center'>".round($value, 2)."%"."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png'></td></tr>";
					 else
					 	echo "<td align='center'>".round($value, 2)."%"."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png'></td></tr>";
					 break;

				case 'High':
					 echo "<tr><td ><b>".$key."<b></td><td align='center'>".$value."</td></tr>";
					 break;

				case 'Low':
					 echo "<tr><td ><b>".$key."<b></td><td align='center'>".$value."</td></tr>";
					 break;

				case 'Open':
					 echo "<tr><td><b>".$key."<b></td><td align='center'>".$value."</td></tr>";
					 break;
				 case 'Status':
					if ($value === "Failure|APP_SPECIFIC_ERROR")
			        {
		        		echo "<tr><td align='center'>No stock information available<i></td></tr>";
						$stop=true;
						break;
			        }
					else
					{
						break;
					}
				case 'MSDate': break;
				default: break;
			}
		}
		}
	echo "</table>";
}
?>
</div>
</body>

	<script>
		function callJason(symbol)
		{
			if(document.getElementById('xml_table'))
				document.getElementById('xml_table').style.display = 'none';
			return ;
		}
		function clearfunction()
		{
			document.getElementById('text_input').value="";
			location.href = 'stock.php';
			return;
		}
	</script>

</html>
