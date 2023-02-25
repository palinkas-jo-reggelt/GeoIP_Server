<?php
	// Database variables
	$Database = array (
		'host'        => 'localhost',
		'username'    => 'geoip',
		'password'    => 'supersecretpassword',
		'dbname'      => 'geoip',
		'table_city'  => 'geocity',           // table name for city ip data
		'table_loc'   => 'citylocations',     // table name for city location data
		'driver'      => 'mysql',
		'port'        => '3306'
	);

	$pdo = new PDO("mysql:host=".$Database['host'].";port=".$Database['port'].";dbname=".$Database['dbname'], $Database['username'], $Database['password']);

	// Regex for IPv4 + IPv6
	$regexIP = "/(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3})|(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])))/";

	function isPrivateOrLoopback($geoip){
		if (preg_match("/^(::1|fc|fd|fe|ff|100)/",$geoip)) {return true;}
		else if (preg_match("/^(10\.|127\.|169\.254|192\.0\.0\.|192\.0\.2\.|192\.88\.99|192\.168|198\.1(8|9)\.|198\.51\.100|203\.0\.113|233\.252\.0|255.255.255.255)/",$geoip)) {return true;}
		else if ((ip2long($geoip) >= ip2long('100.64.0.0')) && (ip2long($geoip) <= ip2long('100.127.255.255'))) {return true;}
		else {return false;}
	}


?>