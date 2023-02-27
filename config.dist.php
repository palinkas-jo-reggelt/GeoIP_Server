<?php

	// Base url
	$base_url = "https://geoip.domain.tld";   // no trailing slash "/", please...

	// Database variables
	$Database = array (
		'host'        => 'localhost',
		'username'    => 'geoip',
		'password'    => 'supersecretpassword',
		'dbname'      => 'geoip',
		'table_city'  => 'geocity',           // table name for city ip data
		'table_loc'   => 'citylocations',     // table name for city location name data
		'driver'      => 'mysql',
		'port'        => '3306'
	);

?>