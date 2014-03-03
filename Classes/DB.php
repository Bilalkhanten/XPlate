<?php
// DB object
// =========
// Contains functions and properties for connecting to the database and executing queries
// NOTES:
// PHP 5.3.0 Added the ability of async queries.
class DB {
	// =======================================================================================================
	// Properties
	// =======================================================================================================
	public static $mysqli;	// Exposes the MySQLi object for use internally and externally
	public static $config;	// Exposes the configuration object for use internally and externally
	public static $query;		// Stores the last query that was executed
	public static $error;		// Exposes any MySQL error messages outside the class

	// =======================================================================================================
	// Functions
	// =======================================================================================================

	// DB::FindConfig Function
	// =======================
	// Locates the configuration file relative to the current directory and loads the configuration properties
	// No arguments
	// Returns TRUE and sets the config properties if the configuration file is found and loaded. Otherwise returns FALSE and the error encountered
	public static function LoadConfig() {
	  $dir = str_replace('\\', '/', dirname(__FILE__)); 			// Get the directory of the current file
	  $filename = $dir . '/config.json'; 			// Create a path to the file in the current dir
	  // Navigate backwards through the directory structure until the config file is found
	  while(is_dir($dir) && !file_exists($filename)) {	// The file is not found in the current directory
	    $dir = ($dir != dirname($dir)) ? dirname($dir) : '';	// Get the parent of the current directory
	    $filename = $dir . '/config.json';			// Create a path to the file in the parent dir
	  }
		// Check if the configuration file exists and attempt to open it
		if(is_readable($filename) && $fconfig = fopen($filename, "r")) {
			// Decode the JSON file to load the config properties							
			self::$config = json_decode(fread($fconfig, filesize($filename)), true);
			fclose($fconfig);							
	  	return true;																					// Return confirmation that the file exists
		} else {																								// If the file doesn't exist or couldn't open
			// Compose the error message
			self::$error = "The configuration file could not be found";
			return false;																					// Return the error message and exit
		}
  }
	// DB::Connect Function
	// ====================
	// Creates a connection to the database with the values stored in the configuration file
	// Arguments:
	// 	$conn => an array with connection properties to be used instead of the config file
	// Returns TRUE if the connection was made or FALSE on error (exposing the error message)
	public static function Connect($conn = '') {
		// Check if the connection string has been provided as an argument and is a valid array
		if(is_array($conn) && array_key_exists('host', $conn) && array_key_exists('username', $conn) && array_key_exists('password', $conn) && array_key_exists('dbname', $conn)) {
			self::$config = $conn; 											// Set the config properties to the provided  argument
		} elseif(!self::LoadConfig()) {								// Attempt to load the config properties from the file
				return false;															// Exit the function if the config is not loaded
		}
		// Use the mysqli::real_connect connection method (see http://us3.php.net/manual/en/mysqli.real-connect.php)
		if(!self::$mysqli = mysqli_init()) {					// Attempt to initialize the database connector
			// Compose the error message
			self::$error = "MySQLi initialization failed";
			return false;																// Return the error message and exit
		}
		if (!self::$mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
			// Compose the error message
    	self::$error = "Setting MYSQLI_INIT_COMMAND failed";
			return false;																// Return the error message and exit
		}
		/*if (!self::$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
			// Compose the error message
	    self::$error = "Setting MYSQLI_OPT_CONNECT_TIMEOUT failed";
		}*/
		// Attempt to connect to the database
		if (!self::$mysqli->real_connect(self::$config['host'], self::$config['username'], self::$config['password'], self::$config['dbname'])) {
			// Compose the error message
  		self::$error = "MySQL failed to connect with the following error: " . mysqli_connect_error();
  		return false;																// Return the error message and exit
		}
		return true;																	// Return confirmation the connection was made and exit
	}

	// DB::Get Function
	// ================
	// Executes a query to return a result
	// Arguments:
	// 	$query => the query to be executed
	// 	$mode  => designates whether a result is being returned (by default) or a record
	// Returns the resulting records if succesfully executed or FALSE if there is an error (exposing the query and error message)
	public static function Get($query, $mode = 'RESULT') {
		if(!isset(self::$mysqli)) {										// If no connection to the DB has been made
			if(!self::Connect())												// Attempt a connection to the DB
				return false;															// Return false if the connection was not made and exit
		}
		if($result = self::$mysqli->query($query)) {	// Attempt to execute the query
  		if($result->num_rows){											// If the query returns values
        if($mode == 'RESULT') {										// If the result is desired
          return $result;													// Return the result
        } elseif($mode == 'RECORD') {							// If only a single record is desired
          $record = $result->fetch_assoc(); 			// Fetch the first record as an associative array
          $result->close();  											// Do some housekeeping and close the result
          return $record;													// Return the record
        }
      }
    	$result->close();														// Do some housekeeping and close the result
		} else { 																			// The query did not execute successfully
			self::$error = self::$mysqli->error; 				// Get the error message
			self::$query = $query;											// Log the query that caused the error for debugging
    }
    return false;																	// Return false if the query could not execute
	}
  // DB::Execute
  // ===========
  // Executes SQL commands like INSERT, UPDATE, CREATE, DROP, etc. which do not return records
  // Arguments:
  // 	$query => the query to be executed.
  // Returns TRUE if the query was executed or FALSE if there is an error (exposing the query and error message)
  public static function Execute($query){
		if(!isset(self::$mysqli)) {										// If no connection to the DB has been made
			if(!self::Connect())												// Attempt a connection to the DB
				return false;															// Return false if the connection was not made and exit
		}
    if($result = self::$mysqli->query($query)) { 	// The query executed successfully.
      return true;
    } elseif(self::$mysqli->errno) { 							// If the query failed due to an error
			self::$error = self::$mysqli->error;				// Get the error message
			self::$query = $query;											// Log the query that caused the error for debugging
    }
    return false;																	// Return false if the query could not execute
  }
  // DB::TableExists
  // ===============
  // Checks for a given table in the database
  // Arguments:
  //  $table  => the name of the table to look for
  // Returns TRUE if the table is found in the db or FALSE if it was not found or there was an error
  public static function TableExists($table){
		if(!isset(self::$mysqli)) {						// If no connection to the DB has been made
			if(!self::Connect())								// Attempt a connection to the DB
				return false;											// Return false if the connection was not made and exit
		}
  	if($result = self::$mysqli->query(		//	Attempt to "show" the table
  	 "SHOW TABLES FROM `" . self::$config['dbname'] . "` 
  	  WHERE `Tables_in_" . self::$config['dbname'] . "` = '$table'")) {
    	if($result->num_rows) {							// If the query returns values
      	$result->close();									// Do some housekeeping and close the result
      	return true;											// Return confirmation that the table exists and exit
    	} else 															// If the query did not return anything
    		$result->close();									// Do some housekeeping and close the result
  	} elseif(self::$mysqli->errno) {			// If the query failed due to an error
			self::$error = self::$mysqli->error;// Get the error message
    	$result->close();  									// Do some housekeeping and close the result
  	}
    return false;													// Return false if the query could not execute
  }
  // DB::FieldExists
  // ===============
  // Check that a column exists in a table
  // Arguments:
  //  $table 	=> the name of the table to check for the existence of the column
  //  $column => the name of the column to look for
  //  Returns TRUE if the field is found in the db or FALSE if it was not found or there was an error
  public static function FieldExists($table, $field){
		if(!isset(self::$mysqli)) {						// If no connection to the DB has been made
			if(!self::Connect())								// Attempt a connection to the DB
				return false;											// Return false if the connection was not made and exit
		}
    if($result = self::$mysqli->query(
     "SHOW COLUMNS FROM `$table` 
     	WHERE `Field` = '$field'")){				// Attempt to "show" the columns
      if($result->num_rows) {							// If the query returns values
        $result->close();									// Do some housekeeping and close the result
        return true;											// Return confirmation that the field exists and exit
      } else 															// If the query did not return anything
      	$result->close();									// Do some housekeeping and close the result
    } elseif(self::$mysqli->errno) {			// If the query failed due to an error
			self::$error = self::$mysqli->error;// Get the error message
	    $result->close();  									// Do some housekeeping and close the result
    }
    return false;													// Return false if the query could not execute
  }
}
?>