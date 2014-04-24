<?php
/**
 * Usage: php export-orders.php --timeFrom=date --timeTo=date --limit=100 --output='myfile' --format='json'
 */

require('../lib/OmetriaAPI/Client.php');

// Load the configuration file (this file must be created following config-example.php).
$config = require('../config.php');

// Create a new instance of the Ometria API Client Class using the configuration file.
$client = new \OmetriaAPI\Client($config);

$query_params = array('limit','offset','timeFrom','timeTo');
$query_string = array();
$query_max    = 250;
$offset       = 0;
// $limit        = 1000;
$page_count   = 0;
$data         = array();
$res;

// how to get commandline params:
$parsed_args = array();
for($i=1; $i<count($argv);$i++){
	$raw = $argv[$i];

	if (substr($raw,0,2)=='--') {
		// This is a --param
		$raw = substr($raw,2);
		$key = strtok($raw, '=');
		$value = strtok('=');
		$parsed_args[$key] = $value;

		if (in_array($key, $query_params)) {
			$query_string[$key] = $value;
		}
	}
}

$offset = $parsed_args['offset'] ?: 0;
// $limit  = $parsed_args['limit'] ?: 100000000;

// algorithm will be:
// 1) Get first <limit> results
// 2) If number of results < <limit> break
// 3) Repeat
// Require the Ometria Client library.

function get_data($offset, $limit) {
	global $res, $client, $query_max, $data, $page_count, $query_string;

	$query_string['offset'] = $offset + ( $page_count * $query_max );
	print_r($query_string);
	$res = $client->get('transactions', $query_string);

	if (is_array($res)) {
		$data = array_merge($data, $res);
		echo(count($res));
		print_r($data);
	}

	$page_count++;

	if (count($data) < $limit || count($res) === $query_max) {
		sleep(1);
		get_data($offset, $limit);
	}
}

get_data($offset, $limit);

echo "\n And the total is....... \n";
echo count($data);
echo "\n";