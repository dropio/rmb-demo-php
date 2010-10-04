<?php

include_once '_bootstrap.php';
include_once 'templates/_helper.php';
include_once '../lib/dropio-php/Dropio/Drop.php';

$type = (isset($_GET['type'])) ? $_GET['type'] : 'all';

if($type == 'all')
{
    $assets = DB::getInstance()->prepare("SELECT * FROM asset WHERE drop_id IN (SELECT id from `drop` WHERE name = ?) ORDER BY type")
                ->execute(array($_GET['drop_name']))
                ->fetchAll();
} else {
    $assets = DB::getInstance()->prepare("SELECT * FROM asset WHERE drop_id IN (SELECT id from `drop` WHERE name = ?) and type = ?")
                ->execute(array($_GET['drop_name'], $type))
                ->fetchAll();
}

$arr = array();
foreach ($assets as $a)
{
    $arr[$a['type']][] = $a;
}


$uploadify_options = array( 'pingback_url' => "{$docroot}pingback.php");

?>
<html>

<head>
<title>Viewing assets for drop <?php echo $_GET['drop_name']?></title>

<!-- The Pretty drop.io stylesheets -->
<link rel="stylesheet" type="text/css" href="../css/base.css"/>
<link rel="stylesheet" type="text/css" href="../css/headers.css"/>
<link rel="stylesheet" type="text/css" href="../css/classes.css"/>
<link rel="stylesheet" type="text/css" href="../css/layout.css"/>

<!-- load jQuery -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>

<!-- Fancybox -->
<script type="text/javascript" src="../utils/fancybox/jquery.fancybox-1.3.1.js"></script>
<link rel="stylesheet" type="text/css" href="../utils/fancybox/jquery.fancybox-1.3.1.css"/>

<!-- Javascript RMB API and streamer -->
<script type="text/javascript" language="javascript" src="js/RichMediaBackboneAPI.js"></script>
<script type="text/javascript" charset="utf-8" src="http://drop.io/javascripts/streamer.js"></script>

<!-- The Audio player -->
<script type="text/javascript" src="../utils/wpaudio/audio-player.js"></script>

<script type="text/javascript" language="javascript">

// Load the audio player
AudioPlayer.setup("<?php echo $docroot ?>/utils/wpaudio/player.swf", {
  width: 290
});

// Initialize the Javascript API Client
var api = new DropioApiClient("<?php echo $API_KEY ?>","<?php echo $docroot ?>/DropioJSClientXDReceiver.html");
var params = { name : "<?php echo $_GET['drop_name'] ?>" };
// End - Javascript API Client

// Fancybox
$(document).ready(function() {

    $(".fancyform").fancybox({
     'type' : 'iframe',
     'onClosed' : function(){
         location.reload();
     }
    });

    // Handle the delete button
    $("#deletedrop").fancybox({
     'type' : 'iframe',
     'onClosed' : function(){
         window.location = '<?php echo $docroot ?>/1-advanced_demo/';
     }
    });

    // We have to force the image-type on all images because fancybox cannot
    // auto-detect it.
    $(".fancyimage").fancybox({
      'type' : 'image'
    });

    $(".fancyaudio").each(function() {
      $(this).fancybox({
        'type' : 'iframe',
        'href' : '<?php echo $docroot ?>/1-advanced_demo/_audio_player.php?file=' + $(this).attr('href') + '&name=' + $(this).attr('name')
      });
    });

    $('.fancydocument').each(function(){
      $(this).fancybox({
        'type' : 'iframe',
        'href' : 'http://docs.google.com/viewer?embedded=true&url=' + $(this).attr('href'),
        'width' : 960,
        'height' : 600
      });
    });

    $('.fancymovie').each(function(){
      $(this).fancybox({
        'padding'   : 0,
        'autoScale' : true,
        'type'      : 'iframe',
        'width'     : 660,
        'height'    : 540,
        'href'      : '<?php echo $docroot ?>/1-advanced_demo/_video_player.php?file=' + $(this).attr('href') + '&poster=' + $(this).attr('poster')
      });
    });

});

</script>
</head>
    <body>
      <div id="layout">
        <a href="/" id="logo"></a>

        <hr class="Solid"/>
          <?php include_once('_slot_drops.php'); ?>

          <a href="<?php echo $docroot ?>/1-advanced_demo">Home</a> &gt; <?php echo $_GET['drop_name']?> &gt; <?php echo $type ?>
          <div style="float: right">
              <?php include_once('_slot_uploadify_form.php'); ?>
          </div>

          <hr class="Dotted"/>

          <a id="deletedrop" href="drop-delete_drop.php?drop_name=<?php echo $_GET['drop_name'] ?>">Delete this drop</a>

          <hr class="Dotted"/>

          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=all">All</a> |
          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=image">Images</a> |
          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=movie">Movies</a> |
          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=audio">Audio</a> |
          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=document">Documents</a> |
          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=notes">Notes</a> |
          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=link">Links</a> |
          <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=other">Other</a>

            <hr class="Dotted"/>

          <div id="content-container">
          <?php foreach ($arr as $k=>$v):   ?>
          <div class="container" id="<?php echo $k ?>-container">
              <h2><?php echo $k?></h2>
              <?php foreach($v as $ass): ?>
                <div class="thumb">
                  <?php $func = "show_$k";echo $func($ass); ?>
                </div>
              <?php endforeach ?>
          </div>
          <?php endforeach ?>
          </div> <!-- END content-container -->
    </div>
  </body>
</html>