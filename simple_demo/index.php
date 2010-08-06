<?php
/**
 * This is a fully functional demo application that lets you manage drops in
 * you drop.io account. It is intended as a functional demonstration of the 
 * underlying PHP API.
 *
 * The online API reference can be found here: http://backbonedocs.drop.io/API-Methods
 *
 * Good luck,
 * 
 * Sheraz Sharif
 * 
 */

# This is for making the demos a little nicer. It has nothing to do with using
# the dropio api.
include_once('_helper.php');

# Get access to the API
include_once('../lib/dropio-php/Dropio/Drop.php');

# Call the configuration file
include_once('config.inc.php');


# Get a list of drops for this account
$drops = Dropio_Api::getInstance($API_KEY, $API_SECRET)->getDrops();

# Get stats associated with this account
$stats = Dropio_Api::getInstance($API_KEY, $API_SECRET)->getStats();

?>
<html>
  <head>
    <title>Drop.io PHP API Simple Demo</title>
    <link type="text/css" rel="stylesheet" media="screen" href="css/demo.css"/>
  </head>
  <body>
    <div id="container">
    <h1>Drop.io API Simple Demo for PHP</h1>
    <p>
      Thank you for downloading our API. These demos provide a simple examples
      which will get you started on making your own apps which hook up to
      the Drop.io media backbone.</p>
    <p>
      This is afully functional demo app that you can use to manage your drops.
      To get started all you need to do is edit the <i>config.php</i> file with
      your <i>api key</i> and <i>api secret</i> if you are using secured keys.
    </p>
    <p>Note: api secret is optional if you are using unsecured api keys.</p>

    <hr/>

    <p>
      <b>Your API_KEY: </b><?php echo $API_KEY ?><br/>
      <b>Your API_SECRET: </b><?php echo $API_SECRET ?>
    </p>

    <hr/>

    <?php if($message): ?>
    <div id="message">
      <?php echo $message ?>
    </div>
    <?php endif ?>

    <div>
      <form action="drop-create_drop.php" method="post">
        <label for="dropname">Create new drop: </label>
        <input type="text" name="dropname"/>
        <input type="submit"/>
      </form>
      <form action="drop-create_drop.php" method="post">
        <input type="hidden" name="dropname" value="random"/>
        <input type="submit" value="Generate Random Drop"/>
      </form>

      <h2>Your Drops</h2>
      <?php foreach($drops['drops'] as $d): ?>
          <div>
            <div class="dropname"><a href="drop-view_assets.php?dropname=<?php echo $d['name']?>" title="View all assets for <?php echo $d['name'] ?>"><?php echo $d['name'] ?></a></div>
            <div class="actionlink edit"><a href="drop-edit_drop.php?dropname=<?php echo $d['name']?>" title="Edit">Edit</a></div>
            <div class="actionlink delete"><a href="drop-delete_drop.php?dropname=<?php echo $d['name']?>" title="Delete">Delete</a></div>
            <div style="clear:both"></div>
          </div>
      <?php endforeach ?>
    </div>

    </div>
  </body>
</html>