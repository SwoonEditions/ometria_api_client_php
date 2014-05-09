<?php
/**
 * Ometria API Client
 *
 * The Client class can be used as an interface to the Ometria API.
 * It is instantiated with the config array returned by config.php as follows:
 *
 *   new \OmetriaAPI\Client(config);
 *
 * The Client class exposes the following method to access the Ometria API:
 * 1. Client->get($resource [,$query]) - Query for records of specified resource (e.g. 'customers', 'transaction').
 *    It accepts a resource as a string and an optional array of query parameters (limit, offset, etc.).
 *
 * 2. Client->post($resource, $data) - Create a new record for a specified resource.
 *    The method accepts a resource as a string and an array with the record data.
 *
 * 3. Client->put($resource, $data) - Update an existing record for a specified resource.
 *    The method accepts a resource as a string and an array with the new record data.
 *
 */

// Namespace the Client class to be part of OmetriaAPI
namespace OmetriaAPI;

// Require APIException for handling API Exceptions from HTTP requests
require(__DIR__.'/APIException.php');

// Define the Client class
class Client {

	var $config=array(
		);

	// Get API key and endpoint as defined in config.php
	public function __construct($config){
		$this->config = array_merge($this->config, $config);
	}

	// Define a public method Client->get
	public function get($resource, $data=array()){

		// Submit a GET request with the $data parameters Array and return the response
		return $this->do_request($resource, 'GET', $data);
	}

	// Define a public method Client->post
	public function post($resource, $data){

		// submit a POST request to create a new record with the $data parameters Array and return the response
		return $this->do_request($resource, 'POST', array(), $data);
	}

	// Define a public method Client->put
	public function put($resource, $data){

		// Submit a PUT request to update an existing record with the $data parameters Array and return the response
		return $this->do_request($resource, 'PUT', array(), $data);
	}

	// Define do_request, an API interface for performing HTTP requests using the Client class
	protected function do_request($resource, $method, $query_string_data=array(), $payload_data=array()){
		// Set URL by the endpoint provided in config.php
		$url = rtrim($this->config['api-endpoint'],'/').'/'.$resource.'.json';

		$query_string_data['nonce'] = (int)(microtime(true) * 1000);

		// Append query string if query parameters are specified in the method call
		if ($query_string_data) $url = $url.'?'.http_build_query($query_string_data);

		// Create new cURL transfer instance
		$ch = curl_init();

		// Return response of the request as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// Set the URL for the request to the endpoint
		curl_setopt($ch, CURLOPT_URL, $url);


		$method = strtoupper($method);
		if ($method!='GET') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		$body_content = '';

		if ($method == 'PUT' || $method=='POST') {
			$body_content = json_encode($payload_data);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body_content);
		}

		// create the request signature by calculating
		// an HMAC-SHA256 hash and encoding it in Base64
		// note the input is the url built earlier
		$sig = base64_encode(hash_hmac('sha256', $url.$body_content, $this->config['api-secret']));

		// Specify request headers
		$headers = array(
		    'Auth-Signature: '. $sig, // calculated signature
		    'Auth-API-Key: '. $this->config['api-key'] // API Key
		);

		// Set request headers equal to $headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// Submit the request and store the response in $json
		$json = curl_exec($ch);

		// Parse the JSON string and store it in $result
		$result = json_decode($json);

		// Print the result JSON in human-readable form
		// print_r($result);

		// Check response status for errors
		if (@$result->status=='OK') {

			// Return the response data if GET or whole reponse otherwise
			return $method=='GET' ? @$result->data : $result;

		} else {

			// Throw API Exception
			throw new APIException(@$result->message);
		}
	}
}