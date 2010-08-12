<?php

# Use sessions to pass around messages without too much trouble
session_start();

# Is there an incoming message?
$message = (!empty($_SESSION['message'])) ? $_SESSION['message'] : false;
unset($_SESSION['message']);


$docroot = 'http://' . $_SERVER["SERVER_NAME"] . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/') + 1);

?>
