<?php

$file = $_GET['file'];
$poster = $_GET['poster'];

$flash_fallback =<<<EOL
<object class='vjs-flash-fallback' width='400' height='325'>
  <param name='allowFullScreen' value='true'>
  <param name='movie' value='../utils/osflv/OSplayer.swf?movie=$file&btncolor=0x333333&accentcolor=0x31b8e9&txtcolor=0xdddddd&volume=30&previewimage=$poster&autoload=off&vTitle=testtitles&showTitle=yes'>
  <embed src='../utils/osflv/OSplayer.swf?movie=$file&btncolor=0x333333&accentcolor=0x31b8e9&txtcolor=0xdddddd&volume=30&previewimage=$poster&autoload=off&vTitle=testtitles&showTitle=yes' width='400' height='325' allowFullScreen='true' type='application/x-shockwave-flash'>
</object>
EOL;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Dropio Video Loader</title>

  <!-- Include the VideoJS Library -->
  <script src="../utils/video-js/video.js" type="text/javascript" charset="utf-8"></script>

  <script type="text/javascript" charset="utf-8">
    // Run the script on page load.

    // If using jQuery
    // $(function(){
    //   VideoJS.setup();
    // })

    // If using Prototype
    // document.observe("dom:loaded", function() {
    //   VideoJS.setup();
    // });

    // If not using a JS library
    window.onload = function(){
      VideoJS.setup();
    }

  </script>
  <!-- Include the VideoJS Stylesheet -->
  <link rel="stylesheet" href="../utils/video-js/video-js.css" type="text/css" media="screen" title="Video JS" charset="utf-8">
</head>
<body>

  <!-- Begin VideoJS -->
  <div class="video-js-box">
    <!-- Using the Video for Everybody Embed Code http://camendesign.com/code/video_for_everybody -->
    <video class="video-js" width="400" height="325" poster="<?php echo $poster ?>" controls preload>
      <source src="<?php echo $file ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
      <?php echo $flash_fallback ?>
    </video>
    <p class="vjs-no-video"></p>
  </div>
  <!-- End VideoJS -->

</body>
</html>