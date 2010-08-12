<?php

# is the databse installed yet? Look for _config.inc.php
if(!file_exists('_config.inc.php'))
    header("Location: /1-advanced_demo/install/");

include_once('_config.inc.php');

# Load the databse class
include_once (dirname(__FILE__) . '/lib/DB.class.php');

# Start a session so we can pass data to the next page load
session_start();

# Is there an incoming message?
$message = (!empty($_SESSION['message'])) ? $_SESSION['message'] : false;
unset($_SESSION['message']);

# Get the current document root
$docroot = 'http://' . $_SERVER["SERVER_NAME"] . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/') + 1);

# Set Debugging
define('DEBUG',true);
?>
