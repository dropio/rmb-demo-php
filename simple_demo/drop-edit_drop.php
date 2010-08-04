<?php

# This is for making the demos a little nicer. It has nothing to do with using
# the dropio api.
include_once('_helper.php');

# Get access to the API
include_once('../lib/dropio-php/Dropio/Api.php');

# Call the configuration file
include_once('config.inc.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']))
  die('$dropname must be present in the request params');

# If the dropname is 'random' then set it to null, otherwise pass it through
$dropname = $_REQUEST['dropname'];

# Call a static method which will create the new drop
$drop = Dropio_Drop::load($dropname);

# Did we get the right drop?
//if($drop->loaded)
  


?>
<html>
  <head>
    <title>Edit Drop | Drop.io PHP API Simple Demo</title>
    <link type="text/css" rel="stylesheet" media="screen" href="css/demo.css"/>
  </head>
  <body>
    <div id="container">

    <?php if($message): ?>
    <div id="message">
      <?php echo $message ?>
    </div>
    <?php endif ?>

    <h1>Editing drop '<?php echo $dropname?>'</h1>
    <form action="drop-update_drop.php" method="post">
      <ul>
        <li>
          <label for="name">Drop name:</label>
          <input type="text" name="name" value="<?php echo $dropname ?>"/></li>
        <li>
          <label for="description">Description:</label>
          <textarea cols="20" rows="10" name="description"><?php echo $drop->description ?></textarea>
        </li>
        <li>
          <input type="hidden" name="orig_name" value="<?php echo $dropname ?>"/>
          <input type="submit"/>
        </li>
      </ul>
    </form>
    </div>
  </body>
</html>