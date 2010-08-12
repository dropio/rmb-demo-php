<?php
# Boostrap this page
include_once('_bootstrap.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']))
  die('$dropname must be present in the request params');

# If the dropname is 'random' then set it to null, otherwise pass it through
$dropname = (strcmp($_REQUEST['dropname'],'random')==0) ? null : $_REQUEST['dropname'];

# Call a static method which will create the new drop
$drop = Dropio_Drop::getInstance($API_KEY,$API_SECRET)->createDrop($dropname);

if($drop->isLoaded())
  $_SESSION['message'] = "SUCCESS! Created new bucket '{$drop->getName()}'";

header("Location: $docroot");

?>
