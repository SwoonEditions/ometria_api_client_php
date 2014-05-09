<?php

// Require the Ometria Client library.
require('../lib/OmetriaAPI/Client.php');

// Load the configuration file (this file must be created following config-example.php).
$config = require('../config.php');

// Create a new instance of the Ometria API Client Class using the configuration file.
$client = new \OmetriaAPI\Client($config);

// Define the order
$order_id = '4322343';
$order = array(
	"is_valid" => true,
	"store" => "store1",
	"subtotal" => 14.32,
	"timestamp" => "2013-10-29T11:58:59+00:00",
	"customer_email" => "joe@gmail.com",
	"shipping" => 1.5,
	"tax" => 0.22,
	"grand_total" => 6.04,
	"currency" => "GBP",
	"customer_firstname" => "Joe",
	"customer_lastname" => "Strummer",
	"customer_id" => "78904",
	"lineitems" => array(
		array(
			"sku" => "V4C3D5R2Z6",
			"quantity" => 1,
			"unit_price" => 2.31,
			"total" => 6.04,
			"tax" => 0.22,
			"subtotal" => 14.32,
			"product_id" => 'item14343243',
			"attributes" => array(
				array(
					"type" => "type1",
					"id" => "id1"
				), array(
					"type" => "type2",
					"id" => "id2"
				)
			)
		)
	)
);

$res = $client->post('transactions/'.$order_id, $order);
print_r($res);

// Test by issuing get to same resource
$retrieved_order = $client->get('transactions/'.$order_id);
print_r($retrieved_order);