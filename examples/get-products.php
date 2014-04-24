<?php
/**
 * GET orders using the Ometria API Client
 *
 * This example uses the Ometria API Client to retrieve 100 transaction records.
 *
 * The get($resource, $query) method takes 2 parameters:
 * - $resource: A string specifiying the resource to be queried.
 * - $query: an optional array of query parameters.
 */

// Require the Ometria Client library.
require('../lib/OmetriaAPI/Client.php');

// Load the configuration file (this file must be created following config-example.php).
$config = require('../config.php');

// Create a new instance of the Ometria API Client Class using the configuration file.
$client = new \OmetriaAPI\Client($config);

// The query Array is constructed, specifying a record limit and offset.
$query_string = array(
	'limit' => 100,
	'offset'=>0
	);

// The get method is called from the client with 'products' as the $resource and query parameters.
$res = $client->get('products', $query_string);

// The API response is printed in human-readable form.
print_r($res);