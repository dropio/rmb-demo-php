<?php

include_once '_bootstrap.php';

$drops = DB::getInstance()->query("SELECT drop_name from asset GROUP BY drop_name ORDER BY drop_name")->fetchAll();
?>

<html>
    <head>
        <title></title>
        <link rel="stylesheet" type="text/css" href="../css/main.css"/>
    </head>
    <body>
        <div id="container">
        <h1>Drop.io Advanced Demo</h1>
        <p>This is a demo of the advanced features of the drop.io API. It uses
            a local data store (MySQL) to cache metadata about your drops and
            assets.
        </p>
        <hr/>
        <h3>Important informaiton:</h3>
        <ul>
            <li>Pingback Url: <?php echo "{$docroot}pingback.php"?></li>
            <li>Total Assets Tracked: </li>
        </ul>
        <hr/>
        <p>Database actions: <a href="drop-import_drop.php">Import a drop</a> | Delete a drop | Empty a drop</p>
        <p>Assets Actions: Add an asset</p>
        <h4>Drops currently in your database</h4>
        <ul>
        <?php foreach ($drops as $d): ?>
            <li><a href="assets-view.php?drop_name=<?php echo $d['drop_name'] ?>"><?php echo $d['drop_name'] ?></a></li>
        <?php endforeach ?>
        </ul>
        <hr/>
        </div>
    </body>
</html>
