<?php

# Load the databse class
include_once (dirname(__FILE__) . '/../lib/DB.class.php');

# Load install-specific configuration
include_once(dirname(__FILE__) . '/../_config.inc.php');

try {
  $sql = "INSERT INTO asset (`drop_id`,`name`,`created_at`,`type`, `is_complete`) values ((SELECT id FROM `drop` d WHERE d.name = ?),?,current_timestamp,?,0)";

  $asset = json_decode(stripslashes($_POST['asset']), true);

  $arr = array($asset['drop_name'], $asset['name'], $asset['type']);

  $db = DB::getInstance();
  $db->prepare($sql);
  $db->execute($arr);

} catch (PDOException $e) {
  echo $e->getMessage();
}

$response['success'] = true;
$response['error'] = $db->getError();
$response['post'] = $_POST;
echo json_encode($response);

?>