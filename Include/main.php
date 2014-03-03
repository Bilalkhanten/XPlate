<?php
// Initialize session. Note: To use cookie-based sessions, session_start() must be called before outputing anything to the browser.
//session_start(); 																					
//date_default_timezone_set('Africa/Nairobi');				// Set the timezone to local time
mb_http_output('UTF-8'); 														// Set internal character encoding to UTF-8
// While running in the development environment...
if($_SERVER['HTTP_HOST'] == 'localhost') {
  error_reporting(E_ALL | E_STRICT); 								// Report all errors. 
  																									// Note: in PHP 5.4.0 E_STRICT became part of E_ALL
}
// Autoload classes
function __autoload($class_name) {
  $dir = str_replace('\\', '/', dirname(__FILE__)); 			// Get the directory of the current file
  $path = $dir . '/Classes/' . $class_name . '.php'; 			// Used to check if a file with the class name exists 
  																												// in the current directory
  // Loop upwards through the directory structure until the class file is found
  while(is_dir($dir) && !file_exists($path)) {								// The file is not found in the current directory
    $dir = ($dir != dirname($dir)) ? dirname($dir) : '';	// Get the parent of the current directory
    $path = $dir . '/Classes/' . $class_name . '.php';		// Check  the parent  directory
  }
  if(file_exists($path)) {																		// If the class file is found
    require_once $path;																		// Include the file with the class name
  } else {																								// If the class file is not found
    echo "Class \"$class_name\" was not found";						// Report that the class file was not found
  }
}
?>