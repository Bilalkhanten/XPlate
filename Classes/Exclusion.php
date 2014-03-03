<?php
// Exclusion object
// ============
class Exclusion {
	// ========================================================================================================
	// Properties
	// ========================================================================================================

	// ========================================================================================================
	// Functions
	// ========================================================================================================
	
	// Exclusion::View function
	// ========================
	// Generates 26,000 registration nos given the 2nd and 3rd character
	// No arguments
	// Returns the query string for the generated registration nos
	public static function View() {
		// Attempt to query for all the exclusions in the database
		if(!$result = DB::Get("SELECT * FROM `exclusion` ORDER BY `type`, `value`")) {
			return false;								// Exit the function if there was an error executing the query. 
																	// Note: the error message is available through the DB object's error property
		} else {											// The query returned a result
			while($record = $result->fetch_assoc()) {
																	// Loop through each record in the result
				switch($record['type']) {	// Check the type of exclusion
					case 'start':						// If it excludes registration nos starting with the value
						break;
					case 'end':							// If it excludes registration nos ending with the values
						break;
					case 'bracket':					// If it excludes registration nos starting and ending with the value
						break;
					case 'any':							// If it excludes any occurence or sequence of the value
						break;
					case 'combination':			// If it excludes any combination of the values
						break;
				}
			}
		}
	}
}
?>