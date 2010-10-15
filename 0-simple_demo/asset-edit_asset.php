<?php

# Boostrap this page
include_once('_bootstrap.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']) || empty($_REQUEST['asset']))
  die('$dropname AND $asset must be present in the request params');

# Get the dropname
$dropname = $_REQUEST['dropname'];

# Get the asset name
$assetname = $_REQUEST['asset'];

# Pick a drop and load the descriptive values into an array
$asset = Rmb_Drop::getInstance($API_KEY, $API_SECRET)
        ->setName($dropname)
        ->getAsset($assetname);

?>
<html>
  <head>
    <title>Edit Asset | Rich Media Backbone PHP API Simple Demo</title>
    <link type="text/css" rel="stylesheet" media="screen" href="../css/main.css"/>
  </head>
  <body>
    <div id="container">

    <?php if($message): ?>
    <div id="message">
      <?php echo $message ?>
    </div>
    <?php endif ?>

    <h1>Editing asset '<?php echo $asset->getName() ?>'</h1>
    <p>
      <?php if ($asset->getType() == 'image'): ?>
         <img src="<?php echo($asset->getRole()->getFileUrl()); ?>" alt=""/>
      <?php endif ?>
    </p>
    <form action="asset-update_asset.php" method="post">
      <ul>
        <li>
          <label for="assetname">Asset name:</label>
          <input type="text" name="assetname" value="<?php echo $asset->getName(); ?>"/></li>
        <li>
          <label for="description">Description:</label>
          <textarea cols="20" rows="10" name="description"><?php echo $asset->getDescription() ?></textarea>
        </li>
        <li>
          <input type="hidden" name="orig_name" value="<?php echo $asset->getName() ?>"/>
          <input type="hidden" name="dropname" value="<?php echo $asset->getDropName() ?>"/>
          <input type="submit"/>
        </li>
      </ul>
    </form>
    </div>
  </body>
</html>
