<?php
# This is for making the demos a little nicer. It has nothing to do with using
# the dropio api.
include_once('_helper.php');

# Get access to the API
include_once('lib/dropio-php/Dropio/Drop.php');

# Call the configuration file
include_once('config.inc.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']))
  die('$dropname must be present in the request params');

# Get the dropname
$dropname = $_REQUEST['dropname'];

# Call a static method which will delete the drop
$r = Dropio_Drop::getInstance($API_KEY,$API_SECRET)->setName($dropname)->delete();

if($r['response']['result'] == 'Success')
  $_SESSION['message'] = "Removed drop '$dropname'";

header("Location: $docroot");

?>