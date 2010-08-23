<script type="text/javascript" src="../utils/wpaudio/audio-player.js"></script>  
<script type="text/javascript">  
AudioPlayer.setup("<?php echo $docroot ?>../utils/wpaudio/player.swf", {  
  width: 290  
});  
</script>  

List | Detailed
<hr/>
<?php foreach ($assets as $k=>$a):   ?>
     <div class="thumb">
         <p id="audioplayer_<?php echo $k ?>">Alt content</p>
         <script type="text/javascript">  
          AudioPlayer.embed("audioplayer_<?php echo $k ?>", {soundFile: "<?php echo get_thumb($a,'original_content') ?>"});  
         </script>  
         <b>Name:</b> <?php echo $a['name'] ?><br/>
         <b>Description:</b> <?php //echo $a['description'] ?>
    </div>
<?php endforeach ?>
