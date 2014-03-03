<?php
// Template object
// ===============
class Template {
	// ========================================================================================================
	// Properties
	// ========================================================================================================
	public static $title = 'XPlate | '; // The value of the <title> tag

	// ========================================================================================================
	// Functions
	// ========================================================================================================
	
	// Template::Header function
	// =========================
	// Generates the markup for the HTML header
	// No arguments
	// Returns the header markup
	public static function Header() {
		$markup = 
'<!DOCTYPE html>
<html>
	<head>
	  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>' . self::$title . '</title>
		<!--CSS-->
		<!--<link href="CSS/src/normalize.css" rel="stylesheet" type="text/css">
		<link href="CSS/src/helper.css" rel="stylesheet" type="text/css">
		<link href="Libraries/jquery-ui/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css">
		<link href="CSS/src/style.css" rel="stylesheet" type="text/css">-->
		<link href="CSS/build/production.css" rel="stylesheet" type="text/css">
		
		<!-- The following override provides support for full multi-stop gradients in IE9 (using SVG). -->
		<!--[if gte IE 9]>
		<style type="text/css">
			.gradient {
				filter: none;
			}
		</style>
		<![endif]-->
		
		<!--jQuery-->
		<!--<script src="Libraries/jquery/jquery-1.11.0.min.js" type="text/javascript"></script>
		<script src="Libraries/jquery/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>-->
		<script src="JS/build/jquery.js" type="text/javascript"></script>

    <!-- HTML5 Shim and Respond.js provide support for HTML5 elements and media queries IE8 and older versions -->
    <!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <!-- <script src="Libraries/html5shiv/dist/html5shiv.js"></script>
    <script src="Libraries/respond.min.js"></script> -->
    <script src="JS/build/ie8.js"></script>
    <![endif]-->
	</head>
	<body>
		<header class="masthead">
			<div class="page-margin">
				<a href="../" class="logo">
					XPlate<span class="slogan">Fun with vehicle registration numbers!</span>
				</a>
				<nav class="mastheadnav">
					<ul>
						<li>
							<a href="./?page=config" class="masthead-config" title="Configure the database">
								<span class="icon masthead-config-icon">&nbsp;</span>Configure the database
							</a>
						</li>
						<li>
							<a href="./?page=rebuild" class="masthead-rebuild" title="Rebuild the database">
								<span class="icon masthead-rebuild-icon">&nbsp;</span>Rebuild the database
							</a>
						</li>
					</ul>
				</nav>
			</div>
		</header>
		<div class="main">
			<div class="page-margin">
			  <div class="content">';
		return $markup;
	}
	// Template::Footer function
	// =========================
	// Generates the markup for the HTML footer
	// Arguments:
	// 	$html => the markup to display within the footer wrapper
	// Returns the header markup
	public static function Footer($html = '') {
		$markup = 
	 	   '</div>
	 	  </div>
	  </div>
	  <footer>
			<div class="page-margin">
				<div class="content">
			  	' . $html . '
			  </div>
			</div>
		</footer>
		<div class="dialog hidden">
			<div class="dialog-rebuild" title="You’re about to make a BIG decision&hellip;">
				<p>Rebuilding the database will clear all existing data and recreate it from scratch. All vehicle registration numbers will be regenerated, but&hellip;</p>
				<p class="highlight">You will lose all your exclusions permanently!</p>
				<p>Are you sure you want to continue?</p>
			</div>
		</div>
		<!-- JS -->
		<!--<script src="Libraries/jquery-ui/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
		<script src="JS/src/main.js" type="text/javascript"></script>-->
		<script src="JS/build/production.js" type="text/javascript"></script>
	</body>
</html>';
		return $markup;
	}
}
?>