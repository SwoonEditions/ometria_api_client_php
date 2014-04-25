<?php
/**
 * Usage: php export-orders.php --timeFrom=date --timeTo=date --limit=100 --output='myfile' --format='json'
 */
echo "Ometria API Client \nPress Ctrl+C to abort.\n\n";

// Load the Ometria API Client class.
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
$parsed_args  = array();
$resource     = 'transactions';
$res;
$export;
$file;

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

echo "Querying for ", $resource, "...\n\n";

// If an offset is specified, add it to the query string, otherwise start from record 0.
$offset = isset($parsed_args['offset']) ? $parsed_args['offset'] : 0;

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

// Convert the result data set to JSON.
$export = json_encode($data, JSON_PRETTY_PRINT) or die("Could not create export file.");

// Create a file to export the data.
$file = "Ometria_API_".$resource."_export";
$extension = ".json";
$i = 1;
$file_name = $file;
// Check if file exists and append a serializer
while(file_exists($file_name.$extension))
{
    $file_name = $file."_".$i;
    $i++;
}

// Set file name to serialized name.
$file = $file_name.$extension;

// Create and pen file with writing permission.
$handle = fopen($file, "wb");

// Write JSON string to $file.
fwrite($handle, $export);

// Close the file after writing.
fclose($handle);

echo "\nData saved to file - ",$file,"\n";
