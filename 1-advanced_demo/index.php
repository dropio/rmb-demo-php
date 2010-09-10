<?php

include_once '_bootstrap.php';

# Get all drops from the database
$drops = DB::getInstance()->
            query("SELECT * FROM `drop`")->
            fetchAll();

?>

<html>
    <head>
        <title>Welcome to the Rich Media Backbone Advanced Demo</title>

        <!-- The Pretty drop.io stylesheets -->
        <link rel="stylesheet" type="text/css" href="../css/base.css"/>
        <link rel="stylesheet" type="text/css" href="../css/headers.css"/>
        <link rel="stylesheet" type="text/css" href="../css/classes.css"/>
        <link rel="stylesheet" type="text/css" href="../css/layout.css"/>

        <!-- Load jQuery -->
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

        <!-- Load Fancybox -->
        <script type="text/javascript" src="../utils/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="../utils/fancybox/jquery.fancybox-1.3.1.css"/>

        <!-- Initialze Fancybox -->
        <script type="text/javascript" language="javascript">
            $(document).ready(function() {
                $(".fancyform").fancybox({
                 'type' : 'iframe',
                 'onClosed' : function(){
                     location.reload();
                 }
                });
            });
        </script>
    </head>
<body>
    <div id="layout">
      <img src="/images/rmb-demo-header.png" alt="Rich Media Backbone Logo"/>
      <p>This is a demo of the advanced features of the RMB API. It uses
          MySQL to cache metadata about your drops and assets.
      </p>
      <hr class="Solid"/>
      <h3>Important information:</h3>
      <ul>
          <li>Pingback Url: <?php echo "{$docroot}/pingback.php"?></li>
      </ul>
      <hr class="Solid"/>
      <p>Database actions: <a class="fancyform" href="drop-create_drop.php">Create drop</a> | <a class="fancyform" href="drop-import_drop.php">Import a drop</a></p>
      <h4>Drops currently in your database</h4>

      <?php if($message): ?>
      <div id="message">
        <?php echo $message ?>
      </div>
      <?php endif ?>

      <?php if(count($drops) > 0): ?>
      <ul>
          <?php foreach ($drops as $d): ?>
              <li><a href="assets-view.php?drop_name=<?php echo $d['name'] ?>"><?php echo $d['name'] ?></a></li>
          <?php endforeach ?>
      </ul>
      <?php else: ?>
    
          <p style="color: red">There are no drops. <a class="fancyform" href="drop-create_drop.php">Create one now.</a></p>
      <?php endif ?>
    
      <hr/>
    </div>

</body>
</html>
