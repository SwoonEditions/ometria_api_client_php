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
$offset       = 0;
$page_size    = 250;
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

//$offset = @$parsed_args['offset'] ?: 0;
$offset = isset($parsed_args['offset']) ? $parsed_args['offset'] : 0;
// $limit  = $parsed_args['limit'] ?: 100000000;

// algorithm will be:
// 1) Get first <limit> results
// 2) If number of results < <limit> break
// 3) Repeat
// Require the Ometria Client library.

function get_data($offset, $limit) {
	global $res, $client, $data, $page_count, $query_string;

	$query_string['limit'] = $limit;
	$query_string['offset'] = $page_count  * $limit;
	print_r($query_string);
	$res = $client->get('transactions', $query_string);

	if (is_array($res)) {
		$data = array_merge($data, $res);
		echo(count($res)),"\n";
		echo(count($data)),"\n";
		//print_r($data);
	}

	$page_count++;
	echo 'page ',$page_count,"\n";

	if (count($res) == $limit) {
		sleep(1);
		get_data($offset, $limit);
	}
}

get_data($offset, $page_size);

echo "\n And the total is....... \n";
echo count($data);
echo "\n";


$page_size  = 250;
$offset = 0;

while(true){
	$qs = array();
	$qs['limit'] = $page_size;
	$qs['offset'] = $offset;
	$res = $client->get('transactions', $query_string);

	// append

	if (count($res)<$page_size) {
		break;
	}

	$offset += $page_size;
}