<?php
header('Content-Type: text/html; charset=utf-8');		// Set character encoding of the document to UTF-8
require_once ('Include/main.php');									// Load the main include file
if(!DB::LoadConfig()) {															// Attempt to load the configuration file for editing
	$page	= 'config';																	// If it fails, load the configuration page
} else {
	$page = (array_key_exists('page', $_GET)) 				// Otherwise check if a page has been requested
						? $_GET['page'] 												// Get the requested page
						: null;																	// Or set to null to load the default page
}
switch($page):
case 'config':
Template::$title .= "Configure the Database";
echo Template::Header(); ?>
			  	<h1 class="font-32">Configure the Database&hellip;</h1>
					<h2 class="font-24">We just need a few simple parameters to help us do our thing!</h2>
					<form action="Function/config.php" id="formconfig" method="post">
						<label class="font-24">
							<span class="label-text">Host:</span>
							<input name="host" required type="text" value="<?php echo (!empty(DB::$config->host)) ? DB::$config->host : 'localhost';?>">
							<span class="hint">The name or address of the database server to connect to</span>
							<span class="clearfix"></span>
						</label>
						<label class="font-24">
							<span class="label-text">Username:</span>
							<input name="username" required type="text" value="<?php echo (!empty(DB::$config->username)) ? DB::$config->username : 'xplate';?>">
							<span class="hint">The name of a user with access to the database (often the same as the DB name. Note: that the user must have the privileges to drop and create tables</span>
							<span class="clearfix"></span>
						</label>
						<label class="font-24">
							<span class="label-text">Password:</span>
							<input name="password" required type="password">
							<span class="hint">The secret password used to access the database.<br><strong>Keyword</strong>: SECRET, i.e. Don't tell anyone ;-)</span>
							<span class="clearfix"></span>
						</label>
						<label class="font-24">
							<span class="label-text">Database Name:</span>
							<input name="dbname" required type="text" value="<?php echo (!empty(DB::$config->dbname)) ? DB::$config->dbname : 'xplate';?>">
							<span class="hint">The name of the database. Plain and simple</span>
							<span class="clearfix"></span>
						</label>
						<div class="button-group">
							<button class="button-default font-24" id="configsubmit" type="submit">OK, I'm ready&hellip;</button>
							<button action="./" class="font-24" id="configcancel" type="button">Perhaps some other time</button>
						</div>
					</form>
<?php
break;
default:
Template::$title .= "Fun with motor vehicle registration numbers!";
echo Template::Header();
// Count the total number of registration numbers in the database
if(!$total = DB::Get("SELECT COUNT(*) AS `count` FROM `numberplate` WHERE `active` = 'Y'", "RECORD")):?>
					<!-- Expose the error message so the user can be alerted if something went wrong -->
					<input class="error-message" type="hidden" value="<?php htmlspecialchars(DB::$error);?>">
					<!-- Capture the query that caused the error -->
					<input class="error-query" type="hidden" value="<?php htmlspecialchars(DB::$query);?>">
<?php
elseif($total['count'] > 0):
// Display the search form and the total number of active registered vehicles?>
					<form class="searchform" action="Function/search.php" method="post">
						<label class="search-label font-32">
							<span class="label-text visually-hidden">Search:</span>
							<input name="search" required type="text" placeholder="K**###*" value="">
							<span class="hint">Enter a registration number using the format given above where (* is any letter from A-Z and # is any number from 0-9</span>
						</label>
						<button class="search-button font-24 button-default" type="submit">Search&hellip;</button>
						<output class="total"><?php echo number_format($total['count']);?></output>
						<div class="total-text">There are currently this many registered vehicles in the database</div>
					</form>
<?php
else:
// Display the progress bar and trigger the generate function?>
					<form class="searchform" action="javascript:;" method="post">
						<input class="generate" type="hidden" value="1">
						<div class="progress-container">
							<div class="progress-bar">
								<div class="progress gradient"></div>
							</div>
							<span class="progress-percent font-32"></span>
							<span class="hint">Our bean counters are working furiously to get you those numbers. It’ll be over before you even know it!</span>
						</div>
						<div class="bean-counter"></div>
						<div class="total"><?php echo number_format($total['count']);?></div>
						<div class="total-text">There are currently this many registered vehicles in the database</div>
						<div class="timer"></div>
					</form>
<?php
endif;
// Count the total number of registration numbers in the database
if(!DB::Get("SELECT * FROM `exclusion`")):
$html =  '<h2>So you were saying something about exclusions?</h2>
					<p>Yes! Exclusions allow you to specify a range of registration numbers to &quot;exclude&quot; from the registry. These excluded numbers will not be added to the total and you can see how many registered vehicles there are without them.</p>
					<p>It’s that simple!</p>
					<a class="exclusion-add" href="javascript:;"><span class="exclusion-add-icon">&nbsp;</span>Just click here to get started&hellip;</a>';
endif;
endswitch;
echo Template::Footer();
?>