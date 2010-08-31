<?php 

$sql = "SELECT d.name as drop_name FROM `drop` d GROUP BY d.name ORDER BY d.name";

$drops = DB::getInstance()->query($sql)->fetchAll();

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
