<?php
/**
 * This is a fully functional demo application that lets you manage drops in
 * you drop.io account. It is intended as a functional demonstration of the
 * underlying PHP API.
 *
 * The online API reference can be found here: http://backbonedocs.drop.io/API-Methods
 *
 */

include_once('_bootstrap.php');

if(!empty($API_KEY) && $API_KEY != 'YOUR_API_KEY') {
  try {
    # Get a list of drops for this account
    $drops = Dropio_Api::getInstance($API_KEY, $API_SECRET)->getDrops();

    # Get stats associated with this account
    $stats = Dropio_Api::getInstance($API_KEY, $API_SECRET)->getStats();
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }
}else{
  $message = "Please provide an API_KEY in config.inc.php";
}

?>
<html>
  <head>
    <title>Drop.io PHP API Simple Demo</title>
    <link type="text/css" rel="stylesheet" media="screen" href="/css/main.css"/>
  </head>
  <body>
    <div id="container">
    <h1>Drop.io API Simple Demo for PHP</h1>
    <p>
      This demo provides a simple examples
      which will get you started on making your own apps which use
      the Drop.io Rich Media Backbone.</p>
    <p>
      This is a fully functional demo app that you can use to manage your drops.
      To get started all you need to do is edit the <i>config.inc.php</i> file with
      your <i>API key</i> and <i>API secret</i> if you are using secured keys.
    </p>
    <p>Note: API secret is optional if you are using unsecured API keys.</p>

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
