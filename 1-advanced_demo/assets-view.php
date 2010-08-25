<?php

include_once '_bootstrap.php';
include_once 'templates/_helper.php';
include_once '../lib/dropio-php/Dropio/Drop.php';

$type = (isset($_GET['type'])) ? $_GET['type'] : 'image';

if($_GET['type'] == 'all')
{

    $assets = DB::getInstance()
                ->prepare("SELECT * FROM asset WHERE drop_name = ? group by type")
                ->execute(array($_GET['drop_name']))
                ->fetchAll();
} else {

    $assets = DB::getInstance()
                ->prepare("SELECT * FROM asset WHERE drop_name = ? and type = ?")
                ->execute(array($_GET['drop_name'], $type))
                ->fetchAll();
}

$uploadify_options = array(
  'pingback_url' => "{$docroot}pingback.php"
);


?>
<html>
    <head>
        <title>Viewing all assets for drop '<?php echo $_GET['drop_name']?></title>
        <link rel="stylesheet" type="text/css" href="../css/main.css"/>

    </head>
    <body>
        <div id="container">
        <?php include_once('_slot_drops.php'); ?>
        
        <h3><?php echo $_GET['drop_name']?> &gt; <?php echo $type ?></h3>

        <hr/>
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=all">All</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=image">Images</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=movie">Movies</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=audio">Audio</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=document">Documents</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=notes">Notes</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=link">Links</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=archive">Archives</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=other">Other</a>
        <div style="float: right">
            <?php //echo Dropio_Drop::getInstance($API_KEY)->setName($_GET['drop_name'])->getUploadifyForm('../utils/',$uploadify_options); ?>
            <?php include_once('_slot_uploadify_form.php'); ?>
        </div>
        <hr/>

        <?php include_once("templates/list-$type.php"); ?>
        </div>
    </body>
</html>
