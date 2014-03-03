<?php
// All string data must be UTF-8 encoded for JSON encoding to work
header('Content-Type: text/html; charset=utf-8');
require_once ('../Include/main.php');			// Load the main include file
// Initialize response variables
$response = array(
	"status"	=> "error",
	"message"	=> "An unknown problem occured prior to initialization!"
);
// Check if the POST variables were successfully submitted
if(empty($_POST['host']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['dbname'])) {
	// Set the response error message
	$response['message'] = "The required POST data was not found!";
	echo json_encode($response);					// Return the response object
	exit;																	// Exit the function
}
// Compile the configuration object with the POST data
$config = array(
	"host"			=> $_POST['host'],
	"username"	=> $_POST['username'],
	"password"	=> $_POST['password'],
	"dbname"		=> $_POST['dbname']
);
if(!DB::Connect($config)) {							// Check if a connection to the DB can be made
	$response['message'] 	= DB::$error;		// Set the response error message
	echo json_encode($response);					// Return the response object
	exit;																	// Exit the function
}
$filename = str_replace('\\', '/', dirname(__DIR__)) . '/config.json';
// Attempt to open or create the file for writing
if(!$fconfig = fopen($filename, 'w')) {								
	// Compose the error message
	$response['message'] = "There was a problem opening the configuration file for writing!";
	echo json_encode($response);					// Return the response object
	exit;																	// Exit the function
} 
// Attempt to write the configuration to the file in JSON encoding
if(!fwrite($fconfig, json_encode($config))) {
	// Compose the error message
	$response['message'] = "There was a problem writing to the configuration file!";
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
}
$response['status'] 	= "success";			// Set the response state
// Set the response message		
$response['message'] 	= "The database has been successfully configured.";					
echo json_encode($response);							// Return the response object
?>