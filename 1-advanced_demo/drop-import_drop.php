<?php

include_once('_bootstrap.php');

# Include the classes for API access
include_once('../lib/dropio-php/Dropio/Drop.php');

include_once('user_funcs.php');

# Has the user chosen a drop?
if (isset($_GET['drop_name']))
{
  $drop_name = $_GET['drop_name'];
  $drop = Dropio_Drop::getInstance($API_KEY)->load($drop_name);
  $assets = $drop->getAssets();

  # Loop over each of the assets, inserting each on into the database
  foreach($assets as $a)
  {
    #var_dump($a->getValues());exit;
    $v = $a->getValues();
    $v['drop_name']=$drop_name;
    asset_updated(json_encode($v));

  }
}



$drops = Dropio_Api::getInstance($API_KEY)->getDrops();

?>
<html>
    <head>
        <title>Import a Drop</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css"/>
    </head>
    <body>
        <div id="container">
        <h1>Import a Drop</h1>
        <p>Choose a drop to import</p>
        <ul>
          <?php foreach($drops['drops'] as $d): ?>
                <li><a href="drop-import_drop.php?drop_name=<?php echo $d['name']?>" title="Import all assets for <?php echo $d['name'] ?>"><?php echo $d['name'] ?></a></li>
          <?php endforeach ?>
         </ul>
         </div>
    </body>

</html>
