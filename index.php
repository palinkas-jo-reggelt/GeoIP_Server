<?php
	include_once('./config.php');
	include_once('./functions.php');

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
		echo "
		<div class='section'>
			<h3>GEOIP</h3>
			<center>
				".$geoip." IS A RESERVED ADDRESS!<br><br>
				Check your own address: <a href='".$base_url."/map/".$_SERVER['REMOTE_ADDR']."'>".preg_replace("/https?:\/\//","",$base_url)."/map/".$_SERVER['REMOTE_ADDR']."</a><br><br>
			</center>
		</div>";
	} else {

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
		while($row = $sql->fetch(PDO::FETCH_ASSOC)){
			$latitude = $row['latitude'];
			$longitude = $row['longitude'];
			if (strlen($row['accuracy_radius'])>0) {$km = " km";} else {$km = "&nbsp;";}
			if ($row['is_in_european_union']==0) {$eu = "false";} else {$eu = "true";}
			$accuracy_radius = $row['accuracy_radius'];
			echo "
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>ip</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$geoip."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>postal_code</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['postal_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>latitude</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['latitude']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>longitude</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['longitude']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>accuracy_radius</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['accuracy_radius'].$km."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>locale_code</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['locale_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>continent_code</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['continent_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>continent_name</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['continent_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>country_code</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['country_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>country_name</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['country_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_1_iso_code</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['subdivision_1_iso_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>state_abbr</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['subdivision_1_iso_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_1_name</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['subdivision_1_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>state_name</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['subdivision_1_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_2_iso_code</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['subdivision_2_iso_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>subdivision_2_name</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['subdivision_2_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>city_name</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['city_name']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>metro_code</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['metro_code']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>time_zone</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$row['time_zone']."&nbsp;</div>
						</div>
						<div class='simple-div-table-row'>
							<div class='simple-div-table-col left'>is_in_european_union</a>&nbsp;</div>
							<div class='simple-div-table-col right'>".$eu."&nbsp;</div>
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
		<div class='section'>
			<center>
				API: <a href='".$base_url."/api/".$geoip."'>".preg_replace("/https?:\/\//","",$base_url)."/api/".$geoip."</a>
			</center>
		</div>

		<script src='https://unpkg.com/leaflet@1.6.0/dist/leaflet.js'></script>
		<script>
			// https://stackoverflow.com/questions/50258902/how-to-zoom-area-within-15km-radius-from-the-marker
			// Create the map
			var map = L.map('map').setView([".$latitude.", ".$longitude."], 12);

			// Set up the OSM layer
			L.tileLayer(
				'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 18
				}
			).addTo(map);

			// add a marker in the given location
			var lat = ".$latitude.";
			var lng = ".$longitude.";
			var marker = L.marker([lat, lng]).addTo(map); // accuracy radius in meters.
			var radius = ".($accuracy_radius * 1000)."; 
			var circleOptions = {
				stroke: true,
				weight: 0.5,
				// color: 'blue',
				// fillColor: '#0000ff',
				// fillOpacity: 0.10
			};
			var circle = L.circle([lat, lng], radius, circleOptions).addTo(map);
			map.fitBounds(circle.getBounds());
		</script>";
	}

	echo "
		<div class='footer'>
			Pálinkás jó reggelt kívánok!<br>
			Powered by <a href='https://www.maxmind.com/'>MaxMind</a> data<br>
			Build your own <a href='https://github.com/palinkas-jo-reggelt/GeoIP_Server'>GeoIP Server</a>";

		(int)$versionGitHub = file_get_contents('https://raw.githubusercontent.com/palinkas-jo-reggelt/GeoIP_Server/main/VERSION');
		(int)$versionLocal = file_get_contents('VERSION');
		if ($versionLocal < $versionGitHub) {
			echo "
			<br><br>Upgrade to version ".str_pad(trim($versionGitHub),3,'0',STR_PAD_LEFT)." available at <a target='_blank' href='https://github.com/palinkas-jo-reggelt/GeoIP_Server'>GitHub</a>";
		}

	echo "
		</div>
	</div>
</body>
</html>";

?>