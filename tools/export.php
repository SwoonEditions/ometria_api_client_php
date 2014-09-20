<?php

require('../lib/OmetriaAPI/Client.php');
require('lib/command-line.php');
require('lib/writers.php');

$arguments = parse_arguments(array(
    'table' => 'customers',
    'format'=>'json',
    'since'=>null
    ), array('output','table'));

if ($arguments===false) {
    echo "Parameters:
    - table (required): What to export (customers,order,products)
    - output (required): Output File Name
    - format - 'json','csv' (JSON is default)
    - since - return rows updated since this date/time";
    echo "(e.g. --output='ometria_export.csv' --format=csv --since='2014-08-01T00:00:00Z')\n\n";

    exit();
}

$table = $arguments['table'];

$allowed_formats = array();
$allowed_formats['json'] = new JSONWriter();

if ($table=='customers'){
    $allowed_formats['csv'] = new CSVWriter(array(
    'id',
    'email',
    'firstname',
    'lastname',
    'name',
    'country',
    'stats.aov'=>'aov',
    'stats.orders'=>'#orders',
    'stats.revenue'=>'clv',
    'stats.visits'=>'#visits',
    'marketing_optin'=>'optin',
    'dates.first_purchase' => 'date_first_order',
    'dates.last_purchase' => 'date_last_order',
    'dates.first_seen' => 'date_first_visit',
    'dates.last_seen' => 'date_last_visit',
    'dates.acquired' => 'date_account_created'
    ));
} elseif ($table=='orders'){
    $allowed_formats['csv'] = new CSVWriter(array(
    'id',
    'customer_email',
    'customer_firstname',
    'customer_lastname',
    'customer_name',
    'customer_id',
    'timestamp',
    'channel',
    'store',
    'shipping_type',
    'payment_method',
    'country_id'=>'country',
    'is_valid',
    'status',
    'currency',
    'grand_total',
    'subtotal',
    'tax',
    'shipping',
    'discount',
    'total_refunded'=>'refunded'
    ));
} elseif ($table=='products'){
    $allowed_formats['csv'] = new CSVWriter(array(
    'id',
    'title',
    'sku',
    'is_variant',
    'is_active'
    ));
}

$resource = $table;
if ($table=='orders') $resource = 'transactions';

$format = $arguments['format'];
if (!isset($allowed_formats[$format])) {
    echo "Format not allowed. Possibile values: ".implode(",", array_keys($allowed_formats))."\n";
}
$writer = $allowed_formats[$format];

$config = require('../config.php');

$client = new \OmetriaAPI\Client($config);

$page_size = 250;
$offset = 0;

$file = fopen($arguments['output'], 'w');
$writer->writeHeaders($file);

$query = array();
$query['limit']=$page_size;
if ($arguments['since']) $query['updateFrom'] = $arguments['since'];

while(true){
    $query['offset']=$offset;

    $res = $client->get($resource, $query);

    foreach($res as $row){
        $writer->write($row, $file);
    }

    $offset += count($res);
    if (count($res)<$page_size) break;
    echo "Exported $offset rows\n";
}
echo "Exported $offset rows\n";
echo "Done\n";

fclose($file);

