<?php

// Namespace the CSV Exporter class to be part of Exporter namespace
namespace Exporter;

/**
*  Exporter subclass to handle CSV formatted file export
*/
class csv extends \Exporter\Exporter {
	var $headers = array("@type", "channel", "country_id", "coupon_code", "currency", "customer_email", "customer_firstname", "customer_lastname", "customer_name", "discount", "grand_total", "id", "ip_address", "is_valid", "payment_method", "shipping", "shipping_type", "subtotal", "tax", "timestamp", "total_refunded");
	var $entry;

	// CSV implementation of format_data
	public function export($handle, $data){
		// Get number of records in data set
		$total = count($data);
		$count = 0;

		echo "Exporting data to CSV file...\n";

		// Append header row to CSV file
		fputcsv($handle, $this->headers);

		// Loop over the API result set to convert array values to CSV rows
		foreach ($data as $record) {
			// Initialize a new array for each csv row
			$row = array();

			// Loop over each $headers and populate the $row array for compatible keys
			foreach ($this->headers as $header) {

				// Conditionally assign the value of $value if the key exists in $record
				$value = property_exists($record, $header) ? $record->{$header} : ' ';

				// Append the value to the row array
				array_push($row, $value);
			}

			// Write array values to CSV
			fputcsv($handle, $row);

			// Increment the written record count
			$count++;

			echo "\r* ", sprintf("%.0f%%", ($count / $total) * 100), " complete...";
		}

			echo "\n Done! \n";
	}
}