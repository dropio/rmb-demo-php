<?php foreach ($assets as $a):   ?>
    <?php $call = "show_{$a['type']}"; ?>
    <?php echo $call($a); ?>
<?php endforeach ?>
