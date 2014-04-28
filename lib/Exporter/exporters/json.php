<?php

// Namespace the JSON Exporter class to be part of Exporter namespace
namespace Exporter;

/**
*  Exporter subclass to handle JSON formatted file export
*/
class json extends \Exporter\Exporter {
	protected function format_data($handle, $data){
		echo 'JSON is Exporoted!';

		// Write JSON string to $file.
		fwrite($handle, json_encode($data, JSON_PRETTY_PRINT));
	}
}
