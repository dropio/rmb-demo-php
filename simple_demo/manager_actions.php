<?php

# Include the config file
include_once('config.inc.php');

# We only need the API object.
include_once('../lib/Dropio-php/Dropio/Api.php');

# This uses the static method
$drops = Dropio_Api::instance($API_KEY)->getDrops();
$stats = Dropio_Api::instance($API_KEY)->getStats();

# That is it! We now have an associative array with meta-data about our drops

?>
<html>
  <head>
    <title>Manager Action: Drop.io Simple Demo PHP API</title>
  </head>
<body>
  <h1>Simple Demo: Manager Account Actions</h1>

  <h2>Account Stats</h2>
  <?php var_dump($stats); ?>

  <h2>List of drops</h2>

  <p>Total Drops: <?php echo $drops['total'] ?></p>
  <p>Page : <?php echo $drops['page'] ?></p>
  <p>Drop listed per page: <?php echo $drops['per_page']; ?></p>
  <p>Your Drops</p>
  <table>
    <thead>
      <tr>
        <th>Drop Name</th>
        <th>Asset Count</th>
        <th>Expiration</th>
        <th>Email</th>
        <th>Admin Token</th>
        <th>Current Bytes</th>
        <th>Max Bytes</th>
        <th>Expiration Date</th>
      </tr>
    </thead>
    <?php foreach($drops['drops'] as $d): ?>
    <tr>
      <td><a href="drop-get_a_drop.php?name=<?php echo $d['name']?>" title="Demo: Show Drops"><?php echo $d['name'] ?></a></td>
      <td><?php echo $d['asset_count'] ?></td>
      <td><?php echo $d['expiration_length'] ?></td>
      <td><?php echo $d['email'] ?></td>
      <td><?php echo $d['admin_token'] ?></td>
      <td><?php echo $d['current_bytes'] ?></td>
      <td><?php echo $d['max_bytes'] ?></td>
      <td><?php echo $d['expires_at'] ?></td>
    </tr>
    <?php endforeach ?>
  </table>
</body>
</html>