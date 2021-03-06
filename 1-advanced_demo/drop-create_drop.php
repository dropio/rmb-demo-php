<?php
# Create a new drop

include_once('_bootstrap.php');

# Include the classes for API access
include_once('../lib/rmb-php/Rmb/Drop.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $drop_name = $_POST['drop_name'];

  try {
    # Lookup the drop in the database;
    $res = DB::getInstance()->
          prepare("SELECT count(*) as count FROM `drop` WHERE name = ? ")->
          execute(array($drop_name))->
          fetch();

    # if not there then make;
    if($res['count'] > 0)
    {
        echo "Drop already exists in database";
    } else {
        echo "Creating drop";
        # Create the drop
        $drop = Rmb_Drop::getInstance($API_KEY, $API_SECRET)->createDrop($drop_name);

        # Insert the drop into the database
        $res = DB::getInstance()->
          prepare("INSERT INTO `drop` (`name`,`values`) VALUES (?,?) ")->
          execute(array($drop_name,json_encode($drop->getValues())));

        $_SESSION['message'] = 'Drop Created!';
    }
    echo '<script type="text/javascript" language="javascript">parent.$.fancybox.close();</script>';
  } catch (Exception $e) {
    echo " failed: " . $e->getMessage();
    echo "<p />";
  }
}
?>
<form action="drop-create_drop.php" method="post">
Drop Name: <input type="text" name="drop_name"/>
<input type="submit"/>
</form>