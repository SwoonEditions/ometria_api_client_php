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

// A data range is defined for the transaction query in two variables, $datetime_from and $datetime_to.
$datetime_from = '2014-04-11 00:00:00';
$datetime_to = '2014-04-11 23:59:59';

// The query Array is constructed, specifying a record limit, offset and date range.
$query_string = array(
	'limit' => 100,
	'offset'=>0,
	'timeFrom' => $datetime_from,	// @todo document in API docs
	'timeTo' => $datetime_to
	);

// The get method is called from the client with 'transactions' as the $resource and query parameters.
$res = $client->get('transactions', $query_string);

// The API response is printed in human-readable form.
print_r($res);