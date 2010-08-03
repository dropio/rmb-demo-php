<?php

# Setup some variables...
$intervals = array(
    '1_DAY_FROM_NOW'         => '1 Day from now',
    '1_WEEK_FROM_NOW'        => '1 Week from now',
    '1_MONTH_FROM_NOW'       => '1 Month from now',
    '1_YEAR_FROM_NOW'        => '1 Year from now',
    '1_DAY_FROM_LAST_VIEW'   => '1 Day from last view',
    '1_WEEK_FROM_LAST_VIEW'  => '1 Week from last view',
    '1_MONTH_FROM_LAST_VIEW' => '1 Month from last view',
    '1_YEAR_FROM_LAST_VIEW'  => '1 Year from last view'
  );

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
          <label for="dropname">Drop name:</label>
          <input type="text" name="dropname" value="<?php echo $dropname ?>"/></li>
        <li>
          <label for="description">Description:</label>
          <textarea cols="20" rows="10">loremipsum</textarea>
        </li>
        <li>
          <label for="expiration">Expiration:</label>
          <select id="expiration" name="expiration">
            <?php foreach($intervals as $option => $text): ?>
              <option value="<?php echo $option ?>" <?php if($drop->expiration_length===$option) echo "selected"?>><?php echo $text ?></option>
            <?php endforeach ?>
          </select>
        </li>
        <li>
          <input type="submit"/>
        </li>
      </ul>
    </form>
    </div>
  </body>
</html>