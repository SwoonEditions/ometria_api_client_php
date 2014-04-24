<?php

require('../lib/OmetriaAPI/Client.php');

$config = require('../config.php');

$client = new \OmetriaAPI\Client($config);

$datetime_from = '2014-03-11 00:00:00';
$datetime_to = '2014-03-11 23:59:59';

$query_string = array(
	'limit' => 100,
	'offset'=>0,
	'timeFrom' => $datetime_from,
	'timeTo' => $datetime_to
	);

$res = $client->get('transactions', $query_string);

print_r($res);