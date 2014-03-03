<?php
header('Content-Type: text/html; charset=utf-8');
																			// All string data must be UTF-8 encoded for JSON encoding to work
require_once('../Include/main.php');			// Load the main include file
$response = array (
	"status"  => "error",
	"message" => "An unknown problem occured prior to initialization!"
);																		// Initialize the response status and message
if(!array_key_exists('i', $_POST) && !(array_key_exists('j', $_POST))) {	
																			// Check that index i has been sent
	$response['message'] = "The required POST data was not found!";
																			// Set the response error message
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
$i = $_POST['i'];											// Use a more readable/manageable alternative for the POST request
$j = $_POST['j'];										
if($i < 0 || $i > 25 || $j < 0 || $j > 25) {								
																			// Check that the indices are not out of range
	$response['message'] = "One or both of the indices are invalid!";
																			// Set the response error message
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
Plate::Generate($i, $j);							// Generate the query to insert reg. nos K$i$j###*
if(DB::Execute("INSERT INTO `numberplate` (`registrationno`) VALUES" . Plate::$query)) {
																			// Attempt to insert 26,000 records into the db
	$response['status'] = "success";
	$response['message'] = "Steady as she goes&hellip;";
																			// Return the progress counter
	if($j < 25) {												// As long as there are characters left,
		$response['i'] = $i;			
		$response['j'] = $j + 1;					// Increment the index for the 3rd character 
	} elseif($i < 25) {
		$response['j'] = 0;								// Return the index for the 3rd character in the next series
		$response['i'] = $i + 1;					// Return the index for the 2nd character in the next series
	} else {
		$response['status'] = "complete";
	}
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
} else {															// If there was an error executing the INSERT query
	$response['message'] = DB::$error;	// Set the response error message
	$response['query']   = DB::$query;	// Add the last executed query to the response to help with debugging
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
?>