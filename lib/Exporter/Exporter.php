<?php

// Namespace the Exporter class to be part of Exporter
namespace Exporter;

// Define the Exporter class
class Exporter {

	// Define a public method Client->get
	public function export($handle, $data, $format){

		// Format the $data through the protected format_data method
		return $this->format_data($handle, $data, $format);
	}

	// Format the $data according to a passed $format, default to JSON.
	protected function format_data($handle, $data, $format){
		echo 'format is - ', $format;
		// Stringify the data in the correct format
		switch ($format) {
			case 'objects':
				$this->export_json($handle, $data);
				break;

			case 'csv':
				$this->export_csv($handle, $data);
				break;

			default:
				$this->export_json($handle, $data);
				break;
		}
	}

	// protected function export_csv($handle, $data, $delimiter = ',', $enclosure = '"') {
	//        $handle = fopen('php://temp', 'r+');

	//        foreach ($data as $line) {
	//        	echo gettype($line);
	//        	print_r($line);
	//                fputcsv($handle, $line, $delimiter, $enclosure);
	//        }

	//        rewind($handle);

	//        while (!feof($handle)) {
	//                $contents .= fread($handle, 8192);
	//        }

	//        fclose($handle);

	//        return $contents;
	// }

	// protected function export_json($handle, $data) {
	// 			echo 'json is exported';

	// 			// Write JSON string to $file.
	// 			fwrite($handle, json_encode($data, JSON_PRETTY_PRINT));
	// }

	// protected function export_json_object($data) {
	//        foreach ($data as $line) {

	//        }

	//        return $contents;
	// }
}