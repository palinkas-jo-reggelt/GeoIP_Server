<?php
	include_once('./config.php');
	include_once('./functions.php');

	if (isset($_GET['geoip'])) {
		if ((filter_var($_GET['geoip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) || (filter_var($_GET['geoip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))) {
			$geoip = $_GET['geoip'];
		} else {
			$geoip = "";
		}
	} else {
		$geoip = "";
	}

	if ($geoip) {
		if (!isPrivateOrLoopback($geoip)) {
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
				$json = array(
					'status'=>200, 
					'message'=>'IP Found',
					'data'=>array(
						'reserved_ip'=>false,
						'ip'=>$geoip, 
						'postal_code'=>$row["postal_code"], 
						'latitude'=>$row["latitude"], 
						'longitude'=>$row["longitude"], 
						'accuracy_radius'=>$row["accuracy_radius"], 
						'locale_code'=>$row["locale_code"], 
						'continent_code'=>$row["continent_code"], 
						'continent_name'=>$row["continent_name"], 
						'country_code'=>$row["country_code"], 
						'country_name'=>$row["country_name"], 
						'subdivision_1_iso_code'=>$row["subdivision_1_iso_code"], 
						'state_abbr'=>$row["subdivision_1_iso_code"], 
						'subdivision_1_name'=>$row["subdivision_1_name"], 
						'state_name'=>$row["subdivision_1_name"], 
						'subdivision_2_iso_code'=>$row["subdivision_2_iso_code"], 
						'subdivision_2_name'=>$row["subdivision_2_name"], 
						'city_name'=>$row["city_name"], 
						'metro_code'=>$row["metro_code"], 
						'time_zone'=>$row["time_zone"], 
						'is_in_european_union'=>$row["is_in_european_union"]
					)
				);
			}
			$rowsReturned = $sql->rowCount();
			if ($rowsReturned > 0) {
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($json, JSON_PRETTY_PRINT);
			} else {
				$json = array(
					'status'=>404, 
					'message'=>'Not Found', 
					'error'=>array(
						'code'=>404,
						'reserved_ip'=>false,
						'ip'=>$geoip,
						'message'=>'IP GeoIP data could not be found'
					)
				);
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($json, JSON_PRETTY_PRINT);
			}
		} else {
			$json = array(
				'status'=>400, 
				'message'=>'Bad Request', 
				'error'=>array(
					'code'=>404,
					'reserved_ip'=>true,
					'ip'=>$geoip,
					'message'=>'IP Reserved'
				)
			);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($json, JSON_PRETTY_PRINT);
		}
	} else {
		$json = array(
			'status'=>400, 
			'message'=>'Bad Request', 
			'error'=>array(
				'code'=>404,
				'reserved_ip'=>false,
				'ip'=>null,
				'message'=>'Non IP input data'
			)
		);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($json, JSON_PRETTY_PRINT);
	}

?>