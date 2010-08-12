<?php

# Boostrap this page
include_once('_bootstrap.php');

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
