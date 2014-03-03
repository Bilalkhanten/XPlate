<?php
// All string data must be UTF-8 encoded for JSON encoding to work
header('Content-Type: text/html; charset=utf-8');
require_once ('../Include/main.php');			// Load the main include file
// Initialize response variables
$response = array(
	"status"	=> "error",
	"message"	=> "An unknown problem occured prior to initialization"
);
if(empty($_POST['hts'])) { 							// Check if the encryption salt was sent in the request
	// Set the response error message
	$response['message'] = "The required POST data was not found";
	echo json_encode($response);					// Return the response object
	exit;																	// Exit the function
}
$hts = stripslashes($_POST['hts']);
if(empty($_COOKIE['xplatetoken'])) {		// Check if the encryption has expired
  // Set the response error message
  $response['message'] = 'Too much time has elapsed! Please retry after the page reloads&hellip;';
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
} 
//Check if the encryption key matches the one generated with the salt
if($_COOKIE['xplatetoken'] != md5('SUP3r53CR3t_53CR3t5QU!rr3L' . $hts)) {
  $response['message'] = 'The security check failed! Please retry after the page reloads&hellip;';
	echo json_encode($response);				// Return the response object
	exit;																// Exit the function
}
if(!DB::TableExists('numberplate')) {		// Check if the numberplate table exists
	if(!DB::Execute(											// Create the numberplate table if it doesn't exist
	 "CREATE TABLE `numberplate` (
		`registrationno` CHAR(7) NOT NULL,
		`rank` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`active` ENUM('Y','N') NOT NULL DEFAULT 'Y',
		`exclusionid` SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
		PRIMARY KEY (`rank`)
	)
	COLLATE='utf8_general_ci'
	ENGINE=MyISAM")) {										// If there is an error executing the query
		$response['message'] 	= DB::$error;	// Set the response error message
		$response['query'] 		= DB::$query;	// Add the last executed query to the response to help with debugging
		echo json_encode($response);				// Return the response object
		exit;																// Exit the function
	} 
} elseif(!DB::Execute("TRUNCATE TABLE `numberplate`")) {	// Execute the query and check for errors
	$response['message']	=	DB::$error;		// Set the response error message
	$response['$query']		= DB::$query;		// Add the last executed query to the response to help with debugging
	echo json_encode($response);					// Return the response object
	exit;																	// Exit the function
}
if(!DB::TableExists('exclusion')) {			// Check if the exclusion table exists
	if(!DB::Execute(											// Create the exclusion table if it doesn't exist
	 "CREATE TABLE `exclusion` (
			`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
			`value` VARCHAR(50) NOT NULL,
			`type` ENUM('start','end','bracket','any','combination') NOT NULL,
			`action` ENUM('add','edit','delete') NOT NULL,
			`actionstate` ENUM('pending','complete') NOT NULL DEFAULT 'pending',
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM")) {
		$response['message'] 	= DB::$error;	// Set the response error message
		$response['query'] 		= DB::$query;	// Add the last executed query to the response to help with debugging
		echo json_encode($response);				// Return the response object
		exit;																// Exit the function
	}
}elseif(!DB::Execute("TRUNCATE TABLE `exclusion`")) {	// Execute the query and check for errors
	$response['message']	=	DB::$error;		// Set the response error message
	$response['$query']		= DB::$query;		// Add the last executed query to the response to help with debugging
	echo json_encode($response);					// Return the response object
	exit;																	// Exit the function
}
$response['status']		= "success";			// Set the response state to successful
echo json_encode($response);						// Return the response object
exit;																		// Exit the function
?>