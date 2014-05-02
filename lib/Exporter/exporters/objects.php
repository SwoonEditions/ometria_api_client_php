<?php

// Namespace the Objects Exporter class to be part of Exporter namespace
namespace Exporter;

/**
*  Exporter subclass to handle JSON Objects formatted file export
*/
class objects extends \Exporter\Exporter {
	public function export($handle, $data){
		echo 'JSON Objects is Exporoted!';

		// Iterate over array to Stringify individual objects.
		foreach ($data as $entry) {
			// Write JSON string to $file.
			fwrite($handle, json_encode($entry)."\n");
		}
	}
}