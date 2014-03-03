<?php
// Search script
// =============
// Queries the rank of a number plate or the range between two registration nos
// Arguments: 
// 	$_POST['search'] => the registration no or range of registration numbers to be searched
// Returns the count of active registration nos between two number plates or the rank of one number plate
header('Content-Type: text/html; charset=utf-8');
																			// All string data must be UTF-8 encoded for JSON encoding to work
require_once('../Include/main.php');			// Load the main include file
$response = array (
	"status"  => "error",
	"message" => "An unknown problem occured prior to initialization!"
);																		// Initialize the response status and message
if(!array_key_exists('search', $_POST)) {	
																			// Check that the number plate or range to be searched has been sent
	$response['message'] = "The required POST data was not found!";
																			// Set the response error message
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
if(!preg_match("/^(K[A-Z]{2}[0-9]{3}[A-Z])(-(K[A-Z]{2}[0-9]{3}[A-Z]))?$/", $_POST['search'], $match)) {
																			// Check that the search string contains one or two valid number plates
	$response['message'] = "The search does not contain a valid registration number!";
																			// Set the response error message
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
$start = $match[1];
if($record = DB::Get("SELECT * FROM `numberplate` WHERE `registrationno` = '$start'", "RECORD")) {																							// Get the record of the start value from the database
	if($record['active'] == 'N') {			// Check if the number plate is excluded from the active registry
		$response['message'] = Exclusion::Get($record['id']);
																			// If so, get the exclusion that applies to the registration and notify the user
		echo json_encode($response);			// Return the response object
		exit;															// Exit the function
	}
} else {															// If there was an error querying the database
	$response['message'] = DB::$error;	// Set the response error message
	$response['query']   = DB::$query;	// Add the last executed query to the response to help with debugging
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
$stop;																// Initialize the stop variable to check for a range
if(!empty($match[3])) {								// If the second registration no has been given, i.e. the user is looking for a range...
	$stop = $match[3];
	if($record = DB::Get("SELECT * FROM `numberplate` WHERE `registrationno` = '$stop'", "RECORD")) {																// Get the record of the stop value from the database
		if($record['active'] == 'N') {		// Check if the number plate is excluded from the active registry
			$response['message'] = Exclusion::Get($record['id']);
																			// If so, get the exclusion that applies to the registration and notify the user
			echo json_encode($response);		// Return the response object
			exit;														// Exit the function
		}
	} else {														// If there was an error querying the database
		$response['message'] = DB::$error;// Set the response error message
		$response['query']   = DB::$query;// Add the last executed query to the response to help with debugging
		echo json_encode($response);			// Return the response object
		exit;															// Exit the function
	}
} elseif($record = DB::Get(
	 "SELECT `registrationno` 
	 	FROM `numberplate` 
	 	WHERE `active` = 'Y' 
	 	ORDER BY `registrationno` LIMIT 1", "RECORD")) {
																			// Query the database for the first active number plate in ascending order
	$stop = $record['registrationno'];	// Assign the first active number plate to the stop value
} else {															// If there was an error querying the database
	$response['message'] = DB::$error;	// Set the response error message
	$response['query']   = DB::$query;	// Add the last executed query to the response to help with debugging
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
if($range = DB::Get(
 "SELECT COUNT(*) AS `count` 
  FROM `numberplate`
  WHERE `active` = 'Y' AND (`registrationno` BETWEEN '$stop' AND '$stop' OR `registrationno` BETWEEN '$stop' AND '$stop'")) {									 // Query for the total number of plates between the start and stop value OR between the stop and start value. Note:This is in case the stop value is lower than the start value
	$response['range'] = number_format($range['count']);
																			// If successful, return the range as the count of all active plates between the start and stop value formatted as ##,###,###
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
} else {
	$response['message'] = DB::$error;	// Set the response error message
	$response['query']   = DB::$query;	// Add the last executed query to the response to help with debugging
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
?>