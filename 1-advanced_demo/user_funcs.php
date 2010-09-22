<?php

/**
 * Log to a local file, because debugging remote APIs is not so easy
 * @param <type> $copy
 */
function dolog($copy)
{
      $f = fopen('log.txt','a');
      fwrite($f,"$copy\n");
      fclose($f);
}

function asset_updated($asset)
{
    # hackish
    global $API_KEY;
    global $API_SECRET;

    dolog('asset_updated() called...raw json string is:'.$asset);

    $values = json_decode(stripslashes($asset),true);

    dolog("Contents of \$asset:".print_r($values, true));

    # Lookup the drop. Create it if it does not exist
    $sql = "SELECT COUNT(id) AS c FROM `drop` WHERE name = ?";

    $r = DB::getInstance();

    $s = $r->prepare($sql)->
                execute(array($values['drop_name']))->
                fetch();

    # Does not exist, create
    if ((int) $s['c'] == 0) {
      # Load the drop first
      # Include the classes for API access
      include_once('../lib/dropio-php/Dropio/Drop.php');
          $drop = Dropio_Drop::getInstance($API_KEY, $API_SECRET)->load($values['drop_name']);

          $sql = "INSERT INTO `drop` (name,`values`) VALUES (?,?)";
          $s = $r->prepare($sql)->
              execute(array($values['drop_name'],$drop->getValues()));
    }

    # Lookup the asset. If it exists then update it. Otherwise, insert it.
    $sql = "SELECT COUNT(a.id) as count
                FROM asset 
                WHERE asset.asset_id = ?";

    $arr = array($values['id']);

    $result = $r->prepare($sql)->
                execute($arr)->
                fetch();

    if ((int) $result['count'] > 0)
    {

        dolog("Found it. updating...");

        $sql = "UPDATE asset a SET a.`values` = ?, a.is_complete = 1
                    WHERE a.asset_id = ?";
        $arr = array(stripslashes($asset),$values['id']);

    } else {

        dolog("Did not find it. Inserting...");


        $sql = "INSERT INTO asset (`drop_id`, `created_at`, `name`, `asset_id`, `values`, `type`,`is_complete`)
            VALUES ((SELECT id FROM `drop` d WHERE d.name = ?),?,?,?,?,?,1)";
        $arr = array(
          $values['drop_name'],
          $values['created_at']['s'],
          $values['name'],
		  $values['id'],
          stripslashes($asset),
          $values['type']
        );
    }

    if(!$r->prepare($sql)->execute($arr))
    {
      dolog('Failed to execute:' . $r->getError());
    }

    return true;
}

function asset_destroyed($asset)
{
  $sql = "DELETE FROM asset WHERE asset_id = ?";
  $values = json_decode(stripslashes($asset),true);

  $arr = array($values['id']);

  DB::getInstance()->prepare($sql)->execute($arr);

}

function job_complete($job){
	dolog(print_r($job, true));
	//throw this away for now;
}