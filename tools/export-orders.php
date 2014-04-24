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

// algorithm will be:
// 1) Get first <limit> results
// 2) If number of results < <limit> break
// 3) Repeat

print_r($parsed_args);