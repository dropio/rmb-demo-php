<?php

//Ensure that we have the required parameters, send an error message if not
if(empty($_POST['asset'])) {
	header("HTTP/1.0 400 Bad request");
	die(json_encode(array("response"=>array("result" => "Error", "message" => "asset_id is a required parameter"))));
}
//Make sure the JSON asset decodes properly
$asset = json_decode(stripslashes($_POST['asset']), true);
if(empty($asset)){
	header("HTTP/1.0 400 Bad request");
	die(json_encode(array("response"=>array("result" => "Error", "message" => "Asset could not be decoded"))));
}

//Set some headers for returning a json response
header('Content-type: application/json');

# Load the database class
include_once (dirname(__FILE__) . '/../lib/DB.class.php');

# Load the conversion requester - this also bootstraps the config, api, etc
include_once(dirname(__FILE__) . '/../request_conversion.php');

# Requset conversion for this asset
request_conversion($asset['id'], $asset['type']);

# Update the database with the new info 
try {
	$sql = "INSERT INTO asset (`drop_id`,`asset_id`,`created_at`,`type`, `is_complete`) values ((SELECT id FROM `drop` d WHERE d.name = ?),?,current_timestamp,?,0)";
	$arr = array($asset['drop_name'], $asset['id'], $asset['type']);
	$db = DB::getInstance();
	$db->prepare($sql);
	$db->execute($arr);
} catch (PDOException $e) {
	die(json_encode(array("response"=>array("result" => "Error", "message" => $e->getMessage()))));
}
die(json_encode(array("response"=>array("result" => "Success", "message" => "Database updated, and conversion requested (if specified) for asset_id " . $asset['id']))));

?>