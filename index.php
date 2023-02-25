<?php
	include_once('./config.php');

	if (isset($_GET['geoip'])) {
		if (preg_match($regexIP,$_GET['geoip'])) {
			$geoip = $_GET['geoip'];
		} else {
			$geoip = $_SERVER['REMOTE_ADDR'];
		}
	} else {
		$geoip = $_SERVER['REMOTE_ADDR'];
	}
	
	echo "
<!DOCTYPE html> 
<html>
<head>
<title>GeoIP</title>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<meta http-equiv='Content-Style-Type' content='text/css'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<link rel='stylesheet' type='text/css' media='all' href='../stylesheet.css'>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css?family=Oswald' rel='stylesheet'> 
<link href='https://unpkg.com/leaflet@1.6.0/dist/leaflet.css' rel='stylesheet'/>
</head>
<body>
	<div class='wrapper'>";

	if (isPrivateOrLoopback($geoip)) {
		echo $geoip." IS A RESERVED ADDRESS!<br><br>Check your own address on the API: <a href='https://geoip.dynu.net/api/".$_SERVER['REMOTE_ADDR']."' target='_blank'>geoip.dynu.net/api/".$_SERVER['REMOTE_ADDR']."</a>";
	} else {

		$sql = $pdo->prepare("
			SELECT * 
			FROM (
				SELECT * 
				FROM ".$Database['table_city']." 
				WHERE INET6_ATON('".$geoip."') <= network_end LIMIT 1
			) AS a 
			INNER JOIN ".$Database['table_loc']." AS b ON a.geoname_id = b.geoname_id 
			WHERE network_start <= INET6_ATON('".$geoip."');
		");
		$sql->execute();

		echo "	
		<div class='section'>
			<h3>GEOIP</h3>
			<div class='secleft'>
				<div class='secinner'>
					<div class='simple-div-table'>
						<div class='simple-div-table-row-header'>
							<div class='simple-div-table-col'>Description</div>
							<div class='simple-div-table-col'>Value</div>
						</div>";

		$sql->execute();
		while($row = $sql->fetch(PDO::FETCH_ASSOC)){
			$latitude = $row['latitude'];
			$longitude = $row['longitude'];
			if (strlen($row['accuracy_radius'])>0) {$km = " km";} else {$km = "&nbsp;";}
			if ($row['is_in_european_union']==0) {$eu = "false";} else {$eu = "true";}
			echo "
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>ip</a>&nbsp;</div><div class='simple-div-table-col right'>".$geoip."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>postal_code</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['postal_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>latitude</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['latitude']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>longitude</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['longitude']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>accuracy_radius</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['accuracy_radius'].$km."</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>locale_code</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['locale_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>continent_code</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['continent_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>continent_name</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['continent_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>country_code</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['country_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>country_name</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['country_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_1_iso_code</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['subdivision_1_iso_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>state_abbr</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['subdivision_1_iso_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_1_name</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['subdivision_1_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>state_name</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['subdivision_1_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_2_iso_code</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['subdivision_2_iso_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_2_name</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['subdivision_2_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>city_name</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['city_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>metro_code</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['metro_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>time_zone</a>&nbsp;</div><div class='simple-div-table-col right'>".$row['time_zone']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>is_in_european_union</a>&nbsp;</div><div class='simple-div-table-col right'>".$eu."</div>
						</div>";
		}

		echo "	
					</div>
				</div>
			</div>
			<div class='secright'>
				<div class='secinner'>
					<div id='map'></div>
				</div>
			</div>
			<div class='clear'></div>
		</div>
		<center>
			API: <a href='https://geoip.dynu.net/api/".$geoip."'>geoip.dynu.net/api/".$geoip."</a>
			<br><br><span style='font-size:0.6em;text-align:center;'>Powered by <a href='https://www.maxmind.com/'>MaxMind</a> data</span><br><br>
		</center>
	</div>

	<script src='https://unpkg.com/leaflet@1.6.0/dist/leaflet.js'></script>
	<script>
		var element = document.getElementById('map');
		var map = L.map(element);
		L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
			attribution: \"&copy; <a href='https://osm.org/copyright'>OpenStreetMap</a> contributors\"
		}).addTo(map);
		var target = L.latLng('".$latitude."', '".$longitude."');
		map.setView(target, 10);
		L.marker(target).addTo(map); 
	</script>";

	}		

	echo "
</body>
</html>";

?>