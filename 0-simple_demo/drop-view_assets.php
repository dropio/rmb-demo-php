<?php

# This is for making the demos a little nicer. It has nothing to do with using
# the dropio api.
include_once('_helper.php');

# Get access to the API
include_once('lib/dropio-php/Dropio/Drop.php');

# Call the configuration file
include_once('config.inc.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']))
  die('$dropname must be present in the request params');

# If the dropname is 'random' then set it to null, otherwise pass it through
$dropname = $_REQUEST['dropname'];

# Call a static method and chain the calls to load the drop
$drop = Dropio_Drop::getInstance($API_KEY,$API_SECRET)->load($dropname);

$assets = $drop->getAssets();

# Did we get the right drop?
//if($drop->loaded)

?>
<html>
  <head>
    <title>View assets for Drop <?php echo $dropname ?> | Drop.io PHP API Simple Demo</title>
    <link type="text/css" rel="stylesheet" media="screen" href="css/demo.css"/>
  </head>
  <body>
    <div id="container">

    <?php if($message): ?>
    <div id="message">
      <?php echo $message ?>
    </div>
    <?php endif ?>

    <h1>Assets for Drop '<?php echo $drop->getName() ?>'</h1>
    <?php echo $drop->getSimpleUploadForm() ?>

    <p>Or use flash uploader</p>
    <?php echo $drop->getUploadifyForm() ?>

    <h2>Files in this drop</h2>
    <table>
    <?php foreach($assets as $a): ?>
    <tr>
      <td><img src="<?php echo $a->getFileUrl('thumbnail') ?>"/></td>
      <td><?php echo $a->getName() ?></td>
      <td>
        <a href="asset-delete_asset.php?dropname=<?php echo $drop->getName() ?>&asset=<?php echo $a->getName() ?>">Delete</a>
        <a href="asset-edit_asset.php?dropname=<?php echo $drop->getName() ?>&asset=<?php echo $a->getName() ?>">Edit</a>
      </td>
    </tr>
    <?php endforeach ?>
    </table>
    </div>
  </body>
</html>