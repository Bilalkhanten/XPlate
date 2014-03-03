<?php
require_once ('../Include/main.php');      // Load the main include file
$ms = (!empty($_GET['ms']) && is_numeric($_GET['ms']) && $_GET['ms'] > 0) ? $_GET['ms'] : 15;
$hts = time();
setcookie('xplatetoken', md5('SUP3r53CR3t_53CR3t5QU!rr3L' . $hts), time() + 60 * $ms, Site::$config['cookie_path'], Site::$config['cookie_domain'], 0); 
# 'Expires' in the past
header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('-1 year')) . ' GMT');
# Always modified
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
# HTTP/1.1
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
# HTTP/1.0
header('Pragma: no-cache');
echo $hts;?>