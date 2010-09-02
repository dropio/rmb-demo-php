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

    dolog('asset_updated() called...');

    $values = json_decode($asset,true);
    ob_start();
    var_dump($values);
    $status = ob_get_contents();
    ob_end_clean();
    
    dolog("Contents of \$asset: $status");
    
    # Lookup the drop. Create it if it does not exist
    $sql = "SELECT COUNT(id) AS c FROM `drop` WHERE name = ?";

    $r = DB::getInstance();

    $s = $r->prepare($sql)->
                execute(array($values['drop_name']))->
                fetch();

    # Does not exist, create
    if ((int) $s['c'] == 0) {
        # Load the drop first
        $drop = Dropio_Drop::getInstance($API_KEY, $API_SECRET)->load($values['drop_name']);
        
        $sql = "INSERT INTO `drop` (name,`values`) VALUES (?,?)";
        $s = $r->prepare($sql)->
            execute(array($values['drop_name'],$drop->getValues()));
    }

    # Lookup the asset. If it exists then update it. Otherwise, insert it.
    $sql = "SELECT COUNT(a.id) as c
                FROM asset a LEFT JOIN `drop` d
                ON d.id = a.drop_id
                WHERE d.name = ? and a.name = ?";
    
    $arr = array($values['drop_name'],$values['name']);

    $s = $r->prepare($sql)->
                execute($arr)->
                fetch();

    if ((int) $s['c'] > 0)
    {

        dolog("Found it. updating...");

        $sql = "UPDATE asset a SET a.`values` = ?, a.is_complete = 1
                    WHERE drop_id IN (SELECT id FROM `drop` d WHERE d.name = ?)
                    AND a.name = ?";
        $arr = array($asset,$values['drop_name'],$values['name']);

    } else {

        dolog("Did not find it. Inserting...");


        $sql = "INSERT INTO asset (`drop_id`, `created_at`, `name`, `values`, `type`,`is_complete`)
            VALUES ((SELECT id FROM `drop` d WHERE d.name = ?),?,?,?,?,1)";
        $arr = array(
          $values['drop_name'],
          $values['created_at'],
          $values['name'],
          $asset,
          $values['type']
        );
    }
    
    if(!$r->prepare($sql)->execute($arr))
    {
      dolog('Filed to execute:' . $r->getError());
    }

    return true;
}

function asset_destroyed($asset)
{
  $sql = "DELETE FROM asset WHERE drop_id IN (SELECT id from `drop` d where d.name = ?) AND name = ?";
  $values = json_decode($asset,true);

  $arr = array($values['drop_name'],$values['name']);

  DB::getInstance()->prepare($sql)->execute($arr);
  
}
