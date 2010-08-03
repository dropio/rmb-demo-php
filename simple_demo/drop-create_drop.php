<?php
# This is for making the demos a little nicer. It has nothing to do with using
# the dropio api.
include_once('_helper.php');

# Get access to the API
include_once('../lib/dropio-php/Dropio/Api.php');

# Call the configuration file
include_once('config.inc.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']))
  die('$dropname must be present in the request params');

# If the dropname is 'random' then set it to null, otherwise pass it through
$dropname = (strcmp($_REQUEST['dropname'],'random')==0) ? null : $_REQUEST['dropname'];

# Call a static method which will create the new drop
$drop = Dropio_Drop::instance($dropname)->save();

if($drop->loaded)
  $_SESSION['message'] = "SUCCESS! Created new bucket '{$drop->name}'";

header("Location: $docroot");

?>