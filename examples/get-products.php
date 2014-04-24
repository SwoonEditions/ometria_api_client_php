<?php

require('../lib/OmetriaAPI/Client.php');

$config = require('../config.php');

$client = new \OmetriaAPI\Client($config);

$query_string = array(
	'limit' => 100,
	'offset'=>0
	);

$res = $client->get('products', $query_string);

print_r($res);