<?php
# Create a new drop

include_once('_bootstrap.php');

# Include the classes for API access
include_once('../lib/dropio-php/Dropio/Drop.php');

$drop_name = $_POST['drop_name'];

# Lookup the drop in the database;
$res = DB::getInstance()->
      prepare("SELECT count(*) as count FROM `drop` WHERE name = ? ")->
      execute(array($drop_name))->
      fetch();

//var_dump($res);exit;

# if not there then make;
if($res['count'] > 0)
{
    echo "Drop already exists in database";
} else {
    echo "Creating drop";
    # Create the drop
    $drop = Dropio_Drop::getInstance($API_KEY)->createDrop($drop_name);
    var_dump($drop);exit;
    
    $res = DB::getInstance()->
      prepare("INSERT INTO `drop` (name,created_at,values) VALUES (?,?,?) ")->
      execute(array($drop_name))->
      fetch();


    var_dump($drop);

}


# Clear out the entries in the database

//header("Location: {$docroot}");