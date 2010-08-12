<?php
# Boostrap this page
include_once('_bootstrap.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']) || empty($_REQUEST['asset']))
  die('$dropname AND $asset must be present in the request params');

# Get the dropname
$dropname = $_REQUEST['dropname'];

# Get the asset name
$asset = $_REQUEST['asset'];

# Call a static method which will delete the drop
$r = Dropio_Asset::getInstance($API_KEY, $API_SECRET)
        ->setDropName($dropname)
        ->setName($asset)
        ->delete();

if($r['response']['result'] == 'Success')
  $_SESSION['message'] = "Removed asset '$asset' in Drop '$dropname'";

header("Location: $docroot");

?>
