<?php
# Include the config file
include_once('config.inc.php');

# We only need the Drop object.
include_once('../lib/Dropio-php/Dropio/Api.php');

# Set a globally accessible API key. It's voodoo.
Dropio_Api::setKey($API_KEY,$API_SECRET);

# Get the drop name from the URL string. Die otherwise
if(empty($_REQUEST['name']))
  die('$name must be present in the REQUEST.');

$name = $_REQUEST['name'];

# This uses the static method
$assets = Dropio_Drop::instance($name)->getAssets();

# That is it! We now have an associative array with meta-data about our drops

?>
<html>
  <head>
    <title>Drop Actions: Get assets for a Drop | Drop.io Simple Demo PHP API</title>
  </head>
<body>
<h1>List Assets for a Drop</h1>

<table>
  <thead>
    <tr>
      <th>Filename</th>
      <th>Type</th>
      <th>Created At</th>
    </tr>

  </thead>
<?php foreach($assets as $a): ?>
  <tr>
  <td><?php echo $a->name ?></td>
  <td><?php echo $a->type ?></td>
  <td><?php echo $a->created_at ?></td>
  </tr>
<?php endforeach ?>
</table>
<?php echo var_dump($a); ?>
</body>
</html>
