<?php
  $file = $_GET['file'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Dropio Video Loader</title>
  <!-- The Audio player -->
  <script type="text/javascript" src="../utils/wpaudio/audio-player.js"></script>

  <script type="text/javascript" language="javascript">

  // Load the audio player
  AudioPlayer.setup("<?php echo $docroot ?>/utils/wpaudio/player.swf", {
    width: 290
  });

  </script>
</head>
<body>
</body>
</html>