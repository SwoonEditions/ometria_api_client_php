<?php

/**
 * Usage: php export-orders.php --timeFrom=date --timeTo=date --limit=100 --output='myfile' --format='json'
 */

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
	}
}

print_r($parsed_args);