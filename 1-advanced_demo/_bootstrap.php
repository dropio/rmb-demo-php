<?php

# is the databse installed yet? Look for _config.inc.php
if(!file_exists(dirname(__FILE__). '/_config.inc.php'))
    header("Location: /1-advanced_demo/install/");

# Load the databse class
include_once (dirname(__FILE__) . '/lib/DB.class.php');

# Load install-specific configuration
include_once('_config.inc.php');

# Start a session so we can pass data to the next page load
session_start();

# Is there an incoming message?
$message = (!empty($_SESSION['message'])) ? $_SESSION['message'] : false;
unset($_SESSION['message']);


$server = 'd.m3b.net';

# Get the current document root
//$docroot = 'http://' . $server . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/') + 1);

?>
