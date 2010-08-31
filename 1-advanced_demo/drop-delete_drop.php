<?php
# Delete a drop

include_once('_bootstrap.php');

# Include the classes for API access
include_once('../lib/dropio-php/Dropio/Drop.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $drop_name = $_POST['drop_name'];

    # Lookup the drop in the database;
    $res = DB::getInstance()->
          prepare("DELETE FROM `drop` WHERE name = ? ")->
          execute(array($drop_name));

    $drop = Dropio_Drop::getInstance($API_KEY)->delete($drop_name);

    $_SESSION['message'] = 'Drop deleted';

    echo '<script type="text/javascript" language="javascript">parent.$.fancybox.close();</script>';
}
?>
<?php if ($_SERVER['REQUEST_METHOD'] == 'GET'): ?>
<form action="drop-delete_drop.php" method="post">
<input type="hidden" name="drop_name" value="<?php echo $_GET['drop_name'] ?>"/>
<input type="submit" value="Delete drop: <?php echo $_GET['drop_name'] ?>"/>
</form>
<?php endif ?>
