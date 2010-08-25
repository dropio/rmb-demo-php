<?php 

$drops = DB::getInstance()->query("SELECT drop_name from asset GROUP BY drop_name ORDER BY drop_name")->fetchAll();

?>

<!-- Begin drops slot -->
<div id="drops">
<h4>Drops</h4>
<ul>
<?php foreach ($drops as $d): ?>
    <li><a href="assets-view.php?drop_name=<?php echo $d['drop_name'] ?>"><?php echo $d['drop_name'] ?></a></li>
<?php endforeach ?>
</ul>
</div>
<!-- End drops slot -->
