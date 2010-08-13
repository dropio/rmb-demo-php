<?php

# This simpel ajax call receives a dropname,
$fp = fopen('ajax-debug.log','a+');
ob_start();
var_dump($_POST);
$str=ob_get_contents();
ob_flush();
fwrite($fp,$str);
fclose($fp);

include_once('../_bootstrap.php');

$sql = "INSERT INTO asset (drop_name,name,created_at,type) values (?,?,?,?)";
$arr = array(
    $_POST['drop_name'],
    $_POST['name'],
    $_POST['created_at'],
    $_POST['type']
);

DB::getInstance()->prepare($sql)->execute($arr);

