<?php
  $file = $_GET['file'];
  $name = $_GET['name'];
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Dropio Audio Player</title>

    <!-- The Audio player -->
    <script type="text/javascript" src="../utils/wpaudio/audio-player.js"></script>

    <script type="text/javascript">
      AudioPlayer.setup("../utils/wpaudio/player.swf", {
        width: 290
      });
    </script>
  </head>
  <body>
    <p id="<?php echo($name); ?>">Alt content</p>
    <script type="text/javascript" language="javascript">AudioPlayer.embed("<?php echo($name); ?>", {soundFile: "<?php echo($file); ?>"});</script>
  </body>
</html>