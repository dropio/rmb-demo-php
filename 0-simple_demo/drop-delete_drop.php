<?php
# Boostrap this page
include_once('_bootstrap.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']))
  die('$dropname must be present in the request params');

# Get the dropname
$dropname = $_REQUEST['dropname'];

# Call a static method which will delete the drop
$r = Rmb_Drop::getInstance($API_KEY,$API_SECRET)->setName($dropname)->delete();

if($r['response']['result'] == 'Success')
  $_SESSION['message'] = "Removed drop '$dropname'";

header("Location: $docroot");

?>
