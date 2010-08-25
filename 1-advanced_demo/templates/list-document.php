<?php foreach ($assets as $a):   ?>
     <div class="thumb">
         <img src="<?php echo get_thumb($a,'web_preview') ?>" alt=""/>
         <?php echo substr($a['name'],0,15) ?><br/>
    </div>
<?php endforeach ?>
