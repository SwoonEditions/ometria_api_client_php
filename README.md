# [Ometria API](http://docs.ometria.com) - PHP Client
An open-source PHP Client by [Ometria](http://www.ometria.com) for accessing the Ometria API.

## General Requirements
You must have a valid API key in order to access the Ometria API.

###Set Up the Configuration File 
Set up the config.php file with your API key & secret.

Follow the config-example.php file included:
 ```php
<?php

return array(
	'api-key'=>'INSERT YOUR API KEY HERE',
	'api-secret'=>'INSERT YOUR API SECRET HERE',
	'api-endpoint' => 'https://api.ometria.com/v1/'
	);

  ```

### Initializing the API Client

Load client.php in your code:

    require('path/to/OmetriaAPI/Client.php');


Load the configuration file:

    $config = require('path/to/config.php');

    
Initialize a Client instance passing it the $config (note it is namespaced inside OmetriaAPI):

    $client = new \OmetriaAPI\Client($config);
    
### Using the API Client

You can use the API Client to submit `GET`, `POST` & `PUT` requests.

####get(resource[, $query])

Use `get()` to retrieve records using the Ometria API.

The `get()` method accepts the following arguments:

- **$resource** - The required resource for the request, as a string.
- **$query** (optional) - An array containing query parameters.

For example, querying the first 100 transaction records:

 ```php
$query = array('limit'=>100, 'offset'=>0);
$res = $client->get('transactions', $query);
 ```


####post(resource, $data)

Use `post()` to set records using the Ometria API.

The `post()` method accepts the following arguments:

- **$resource:id** - The required resource for the request, as a string. 
- **$data** - An array with new data records to post.

For example, to create a new record:

 ```php
data = array(
	     'timestamp'   => '2013-10-29T11:58:59+00:00',
	     'subtotal'    => 12.34,
	     'shipping'    => 1.00,
	     'tax'         => 0.5,
	     'grand_total' => 15,
	     'currency'    => 'GBP',
	     'line_itmes'  => array(LINE_ITEMS)
		);
	      
$res = $client->post('/transactions/{transaction_id}', $data);
 ```
 
### Further Documentation
Extended documentation can be found in the [Ometria Docs](http://docs.ometria.com) page.
 
 
# Ometria API Client Orders Exporter
 
The orders exporter is an example PHP implementation of the API Client.
It can be used to retrieve transaction records and save them to file.

## Using the Orders Exporter
To use the Orders Exporter simply launch the tools\export-orders.php file in the php CLI:

    php export-orders.php

You can always follow on-screen instructions to use it.

The Exporter accepts the following parameters:
- **output** (required): Output File Name
- **timeFrom** (required) - 'YYYY-MM-DD'
- **timeTo** (required) - 'YYYY-MM-DD'
- **format** - 'json','objects' or 'csv' (JSON is default)

##### Note that CSV mode does not retrieve line items for orders.

Parameters can be listed sequentially in the following format:

    --parameter=value
    
For example, exporting all orders in 2013 to a CSV file '2013-orders.csv':
 ```bash
php export-orders.php --format='csv' --output='2013-orders.csv' --timeFrom='2013/1/1' --timeTo='2013/12/31'
  ```
