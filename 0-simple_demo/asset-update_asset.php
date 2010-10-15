<?php
# Boostrap this page
include_once('_bootstrap.php');


# Bail out if we didn't get a name with it.
if(!isset($_POST['assetname']))
  die('$name must be present in the request params - ' . $_POST['name']);

$asset = Rmb_Asset::getInstance($API_KEY,$API_SECRET)
        ->setDropName($_POST['dropname'])
        ->setName($_POST['assetname'])
        ->load()
        ->setName($_POST['assetname'])
        ->setDescription($_POST['description'])
        ->save();

$_SESSION['message'] = "Update successful!";

header("Location: {$docroot}drop-view_assets.php?dropname={$_POST['dropname']}");
?>
