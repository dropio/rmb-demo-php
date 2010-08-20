<?php

include_once '_bootstrap.php';
include_once 'templates/_helper.php';
include_once '../lib/dropio-php/Dropio/Drop.php';

$type = (isset($_GET['type'])) ? $_GET['type'] : 'image';

$assets = DB::getInstance()
            ->prepare("SELECT * FROM asset WHERE drop_name = ? and type = ?")
            ->execute(array($_GET['drop_name'], $type))
            ->fetchAll();

$uploadify_options = array(
  'pingback_url' => "{$docroot}pingback.php"
);

$droplist = Dropio_Api::getInstance($API_KEY)->getDrops();

?>
<html>
    <head>
        <title>Viewing all assets for drop '<?php echo $_GET['drop_name']?></title>
        <link rel="stylesheet" type="text/css" href="../css/main.css"/>
        <script type="text/javascript" language="Javascript" src="js/jquery.js"></script>
        <script type="text/javascript" language="javascript" src="js/fancybox.js"></script>
        <script type="text/javascript" language="javascript">
         document.onready(function(){


         });


        </script>

    </head>
    <body>
        <div id="container">
        <div id="drops">
          <h4>Drops</h4>
          <ul>
            <?php foreach ($droplist['drops'] as $d): ?>
              <li><?php echo $d['name'] ?></li>
            <?php endforeach ?>
          </ul>
        </div>
        <h1>Viewing type '<?php echo $type ?>' for drop '<?php echo $_GET['drop_name']?></h1>

        <hr/>
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=image">Images</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=movie">Movies</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=audio">Audio</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=document">Documents</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=notes">Notes</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=link">Links</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=archive">Archives</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=other">Other</a>
        <div style="float: right">
            <?php echo Dropio_Drop::getInstance($API_KEY)->setName($_GET['drop_name'])->getUploadifyForm('../utils/',$uploadify_options); ?>
        </div>
        <hr/>

        <?php include_once("templates/list-$type.php"); ?>
        </div>
    </body>
</html>
