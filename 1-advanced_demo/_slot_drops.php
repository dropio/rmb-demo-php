<?php $droplist = Dropio_Api::getInstance($API_KEY)->getDrops(); ?>
<!-- Begin drops slot -->
<div id="drops">
<h4>Drops</h4>
<ul>
<?php foreach ($droplist['drops'] as $d): ?>
<li><a href="assets-view.php?drop_name=<?php echo $d['name'] ?>"><?php echo $d['name'] ?></a></li>
<?php endforeach ?>
</ul>
</div>
<!-- End drops slot -->
