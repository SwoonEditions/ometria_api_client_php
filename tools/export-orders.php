<?php
/**
 * Usage: php export-orders.php --timeFrom=date --timeTo=date --output='myfile' --format='json'
 */
echo "\033[1;31m Ometria API Client \033[0m \nPress Ctrl+C to abort.\n\n";

echo "Accecpted parameters:\n- output (required): Output File Name \n- format - 'json','objects' or 'csv' (JSON is default)\n- timeFrom (required) - 'YYYY/MM/DD'\n- timeTo (required) - 'YYYY/MM/DD'\n\n";

echo "(e.g. --output='ometriaOrdersExport')\n\n";

// Load the Ometria API Client and Exporter classes.
require('../lib/OmetriaAPI/Client.php');
require('../lib/Exporter/Exporter.php');
require('../lib/Exporter/exporters/json.php');
require('../lib/Exporter/exporters/objects.php');
require('../lib/Exporter/exporters/csv.php');

// Load the configuration file (this file must be created following config-example.php).
$config = require('../config.php');

// Create a new instance of the Ometria API Client Class using the configuration file.
$client = new \OmetriaAPI\Client($config);

$query_params = array('limit','offset','timeFrom','timeTo');
$query_string = array();
$req_params   = array('output'=>0,'timeFrom'=>0, 'timeTo'=>0);
$offset       = 0;
$page_size    = 250;
$page_count   = 0;
$data         = array();
$parsed_args  = array();
$resource     = 'transactions';

// Parse CLI arguements
for($i=1; $i<count($argv);$i++){
	$raw = $argv[$i];

	if (substr($raw,0,2)=='--') {
		// This is a --param
		$raw = substr($raw,2);
		$key = strtok($raw, '=');
		$value = strtok('=');

		// Store arguemnt key:value in $parsed_args array.
		$parsed_args[$key] = $value;

		// If the arguement is a valid query parameter, add it to the query string.
		if (in_array($key, $query_params)) {
			$query_string[$key] = $value;
		}
	}
}

// Check if all required arguements are provided
$param_diff = array_diff_key($req_params, $parsed_args);

// Aborts if any required parameters are not provided
if (count($param_diff) > 0) {
	echo "\033[41m Missing Parameters: ", implode(', ', array_keys($param_diff)), ". \033[0m \n";
	die();
}

echo "Querying for ", $resource, "...\n\n";

// If a format is specified use it, otherwise default to JSON.
$format = isset($parsed_args['format']) ? strtolower($parsed_args['format']) : 'json';

// Set the limit of the query to the maximum page size (specified to assure operation of the client).
$query_string['limit'] = $page_size;

// Query the API for resource records
while(true){
	// re-set the offset to retrieve the next batch of records by page size.
	$query_string['offset'] = $offset;

	// Submit a get request to the Ometria API for the required resource and query parameters.
	$res = $client->get('transactions', $query_string);

	// Check if the API response returned a valid array.
	if (is_array($res)) {
		echo "\r* Retrieved ", $resource, " records ", ($offset - count($data)), " to ", ($offset + count($res)), "...";

		// Merge the responce into the previously retrieved data set.
		$data = array_merge($data, $res);
	}

	// Increment the offset by the page size retrieved.
	$offset += $page_size;

	// Check if the response array's length matches the page size.
	if (count($res)<$page_size) {
		// Break out of the while loop if the responce is smaller than the page size (means that the end of the data set was reached).
		break;
	}
}

echo "\n\n Completed data fetch. \n Retrieved a total of ", count($data), " ", $resource," records.\n";

// Instantiate the Exporter class.
$exporter = "\Exporter\\".$format;
$export = new $exporter();

// Create a file to export the data.
$file = $parsed_args['output'];

// Create and pen file with writing permission.
$handle = fopen($file, "wb");

// write export data to file.
$export->export($handle, $data, $format);

// Close the file after writing.
fclose($handle);

echo "\nData saved to file - ",$file,"\n";
