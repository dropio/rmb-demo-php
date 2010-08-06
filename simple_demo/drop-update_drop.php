<?php

# This is for making the demos a little nicer. It has nothing to do with using
# the dropio api.
include_once('_helper.php');

# Get access to the API
include_once('../lib/dropio-php/Dropio/Drop.php');

# Call the configuration file
include_once('config.inc.php');

# Bail out if we didn't get a name with it.
if(empty($_POST['name']))
  die('$name must be present in the request params');

$drop = Dropio_Drop::getInstance($API_KEY,$API_SECRET)->load($_POST['orig_name'])
  ->setName($_POST['name'])
  ->setDescription($_POST['description'])
  ->save();

$_SESSION['message'] = "Update successful!";

header("Location: $docroot");
?>