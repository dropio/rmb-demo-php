<?php

include_once '_bootstrap.php';
include_once 'templates/_helper.php';

$type = (isset($_GET['type'])) ? $_GET['type'] : 'image';

$assets = DB::getInstance()
            ->prepare("SELECT * FROM asset WHERE drop_name = ? and type = ?")
            ->execute(array($_GET['drop_name'], $type))
            ->fetchAll();

?>
<html>
    <head>
        <title>Viewing all assets for drop '<?php echo $_GET['drop_name']?></title>
        <link rel="stylesheet" type="text/css" href="/css/main.css"/>
    </head>
    <body>
        <div id="container">
        <h1>Viewing type '<?php echo $type ?>' for drop '<?php echo $_GET['drop_name']?></h1>

        <hr/>
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=image">Images</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=movie">Movies</a> |
        | Audio | Documents | Notes | Links | Archive | Other
        <div style="float: right">Upload New</div>
        <hr/>

        <?php include_once("templates/list-$type.php"); ?>
        </div>
    </body>
</html>