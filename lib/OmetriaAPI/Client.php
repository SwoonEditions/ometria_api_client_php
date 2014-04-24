<?php

namespace OmetriaAPI;

require(__DIR__.'/APIException.php');

class Client {

	var $config=array(
		);

	public function __construct($config){
		$this->config = array_merge($this->config, $config);
	}

	public function get($resource, $data=array()){
		return $this->do_request($resource, 'GET', $data);
	}

	public function post($resource, $data){
		return $this->do_request($resource, 'POST', array(), $data);
	}

	public function put($resource, $data){
		return $this->do_request($resource, 'PUT', array(), $data);
	}

	protected function do_request($resource, $method, $query_string_data=array(), $payload_data=array()){
		$url = rtrim($this->config['api-endpoint'],'/').'/'.$resource.'.json';

		$query_string_data['nonce'] = (int)(microtime(true) * 1000);

		if ($query_string_data) $url = $url.'?'.http_build_query($query_string_data);

		$ch = curl_init(); // create curl instance
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // we want the response contents

		curl_setopt($ch, CURLOPT_URL, $url);

		// create the request signature by calculating
		// an HMAC-SHA256 hash and encoding it in Base64
		// note the input is the url built earlier
		$sig = base64_encode(hash_hmac('sha256', $url, $this->config['api-secret']));

		// specify request headers
		$headers = array(
		    'Auth-Signature: '. $sig, // calculated signature
		    'Auth-API-Key: '. $this->config['api-key'] // API Key
		);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set the headers

		$json = curl_exec($ch); // run request

		$result = json_decode($json);

		print_r($result);

		if (@$result->status=='OK') {
			return @$result->data;
		} else {
			throw new APIException(@$result->message);
		}
	}
}