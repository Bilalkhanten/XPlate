<?php
// Plate object
// ============
class Plate {
	// ========================================================================================================
	// Properties
	// ========================================================================================================
	public static $query;										// Initialize the query string
	public static $alpha = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
																					// Initialize the array of alpha numeric characters (A-Z and 0-9) as string literals
	// ========================================================================================================
	// Functions
	// ========================================================================================================
	
	// Plate::Generate function
	// ========================
	// Generates 26,000 registration nos given the 2nd and 3rd character
	// Arguments:
	// 	$i => the index of the 2nd character in the registration no
	// 	$j => the index of the 3rd character in the registration no
	// Returns the query string for the generated registration nos
	public static function Generate($i, $j) {
		for($k = 0; $k <= 25; $k++) {					// Loop index k from 0 to 25 representing iterations of the 7th character K**###X from (A-Z), e.g. KAA999A » KAA000B
			for($x = 26; $x <= 35; $x++) {			// Loop index x from 26 to 35 representing iterations of the 1st digit K**1##* from (0-9), e.g. KAA099A » KAA100A
				for($y = 26; $y <= 35; $y++) {		// Loop index y from 26 to 35 representing iterations of the 2nd digit K**#2#* from (0-9), e.g. KAA009A » KAA010A
					for($z = 26; $z <= 35; $z++) {	// Loop index z from 26 to 35 representing iterations of the 3rd digit K**##3* from (0-9), e.g. KAA000A » KAA001A
						if(!empty(self::$query)) 			// If the query string already contains values, 
							self::$query .= ",";				// add a comma at the end to separate the next value
						self::$query .= "('K" . self::$alpha[$i] . self::$alpha[$j] . self::$alpha[$x] . self::$alpha[$y] . self::$alpha[$z] . self::$alpha[$k] . "')";
																					// Create the registration number by combining the string literal 'K' with the current value of each index in the array of alphanumerics
					}																// End the loop for the third digit (K**##3*)
				} 																// End the loop for the second digit (K**#2#*)
			}																		// End the loop for the first digit (K**1##*)
		} 																		// End the loop for the seventh character (K**###X)
	}
	// Plate::Total function
	// =====================
	// Counts the total number of registration numebrs in the database
	// Arguments:
	// 	$active => a boolean that indicates whether to check for active records or all number plates (true by default)
	// Returns the total number of registration numbers according to the condition specified
	public static function Total($active = true) {
		$query = "SELECT COUNT(*) AS `count` FROM `numberplate`";
																					// Build the query string to find the total number plates
		if($active) $query .= " WHERE `active` = 'Y'";
																					// Set the query to look for active numberplates otherwise unless specified
		if($total = DB::Get($query, "RECORD")) {
																					// Attempt to get the total from the database
			return $total['count'];							// Return the total if successful and exit
		}
		return false;													// If there was an error executing the query, return false and exit. Note: the error message is available through the DB object's error property
	}
	// Plate::Validate function
	// ========================
	// Checks whether a string meets the criteria to be considered a number plate
	// Arguments: $plate => the registration number to be checked for validity
	// Returns a boolean indicating whether or not the number plate is valid
	public static function Validate($plate) {
		$plate = strtoupper($plate);					// Convert the string to uppercase
		return(preg_match('/^K[A-Z]{2}[0-9]{3}[A-Z]$/', $plate));
    																			// Check if the string is exactly 7 characters long, the 2nd, 3rd and 7th characters are letters, and the 4th, 5th and 6th characters are numbers
	}
}
?>