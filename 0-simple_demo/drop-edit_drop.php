<?php
# Boostrap this page
include_once('_bootstrap.php');

# Bail out if we didn't get a name with it.
if(empty($_REQUEST['dropname']))
  die('$dropname must be present in the request params');

# If the dropname is 'random' then set it to null, otherwise pass it through
$dropname = $_REQUEST['dropname'];

# Pick a grep and load the descriptive values into an array
$drop = Dropio_Drop::getInstance($API_KEY, $API_SECRET)->load($dropname);

?>
<html>
  <head>
    <title>Edit Drop | Rich Media Backbone PHP API Simple Demo</title>
    <link type="text/css" rel="stylesheet" media="screen" href="css/demo.css"/>
  </head>
  <body>
    <div id="container">

    <?php if($message): ?>
    <div id="message">
      <?php echo $message ?>
    </div>
    <?php endif ?>

    <h1>Editing drop '<?php echo $drop->getName() ?>'</h1>
    <form action="drop-update_drop.php" method="post">
      <ul>
        <li>
          <label for="name">Drop name:</label>
          <input type="text" name="name" value="<?php echo $dropname ?>"/></li>
        <li>
          <label for="description">Description:</label>
          <textarea cols="20" rows="10" name="description"><?php echo $drop->getDescription() ?></textarea>
        </li>
        <li>
          <input type="hidden" name="orig_name" value="<?php echo $drop->getName() ?>"/>
          <input type="submit"/>
        </li>
      </ul>
    </form>
    </div>    
  </body>
</html>
