<?php

include_once('_bootstrap.php');

# Include the classes for API access
include_once('../lib/dropio-php/Dropio/Drop.php');


include_once('user_funcs.php');

# Has the user chosen a drop?
# Note: If there are no assets in the drop then nothing will be inserted.
# TODO: If there are no assets then we need to create a note asset
if (isset($_GET['drop_name']))
{
  $drop_name = $_GET['drop_name'];
  $drop = Dropio_Drop::getInstance($API_KEY)->load($drop_name);
  $assets = $drop->getAssets();


  # Insert the drop
  $r = DB::getInstance();
  $sql = "INSERT INTO `drop` (name,`values`) VALUES (?,?)";
  $s   = $r->prepare($sql)->execute(array($drop->getName(),json_encode($drop->getValues())));

  # Loop over each of the assets, inserting each on into the database
  foreach($assets as $a)
  {
    #var_dump($a->getValues());exit;
    $v = $a->getValues();
    $v['drop_name']=$drop_name;
    asset_updated(json_encode($v));
  }
  
  $_SESSION['message'] = 'Drop Imported!';

  echo '<script type="text/javascript" language="javascript">parent.$.fancybox.close();</script>';

}

$drops = Dropio_Api::getInstance($API_KEY, $API_SECRET)->getDrops();

?>
<html>
    <head>
        <title>Import a Drop</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css"/>
    </head>
    <body>
        <p>Choose a drop to import</p>
        <ul>
          <?php foreach($drops['drops'] as $d): ?>
                <li><a href="drop-import_drop.php?drop_name=<?php echo $d['name']?>" title="Import all assets for <?php echo $d['name'] ?>"><?php echo $d['name'] ?></a></li>
          <?php endforeach ?>
         </ul>
    </body>

</html>
