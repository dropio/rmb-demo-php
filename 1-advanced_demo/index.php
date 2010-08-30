<?php

include_once '_bootstrap.php';

$drops = DB::getInstance()->
            query("SELECT * FROM `drop`")->
            fetchAll();

var_dump($drops);
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
            MySQL to cache metadata about your drops and assets.
        </p>
        <hr/>
        <h3>Important information:</h3>
        <ul>
            <li>Pingback Url: <?php echo "{$docroot}pingback.php"?></li>
        </ul>
        <hr/>
        <p>Database actions: <a href="drop-import_drop.php">Import a drop</a> | Delete a drop </p>
        <h4>Drops currently in your database</h4>
        <?php if(count($drops) > 0): ?>
        <ul>
            <?php foreach ($drops as $d): ?>
                <li><a href="assets-view.php?drop_name=<?php echo $d['name'] ?>"><?php echo $d['name'] ?></a></li>
            <?php endforeach ?>
        </ul>
        <?php else: ?>
        
            <p style="color: red">There are no drops. Create one now.
                <form action="drop-create_drop.php" method="post">
                    Drop Name: <input type="text" name="drop_name"/>
                    <input type="submit"/>
                </form>
            </p>
        <?php endif ?>
        
        <hr/>
        </div>

    </body>
</html>
