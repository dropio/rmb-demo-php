<script type="text/javascript" src="../utils/wpaudio/audio-player.js"></script>  
<script type="text/javascript">  
AudioPlayer.setup("<?php echo $docroot ?>../utils/wpaudio/player.swf", {  
  width: 290  
});  
</script>  

<?php foreach ($assets as $k=>$a):   ?>
     <div class="thumb">
     <?php echo show_image($a); ?>
     </div>
<?php endforeach ?>
