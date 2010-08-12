<?php foreach ($assets as $a):   ?>
     <div class="thumb">
         <img src="<?php echo get_thumb($a,'thumbnail') ?>" alt=""/>
         <b>Name:</b> <?php echo $a['name'] ?><br/>
         <b>Description:</b> <?php //echo $a['description'] ?>
    </div>
<?php endforeach ?>