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

    dolog('asset_updated() called...');
    

    $values = json_decode($asset,true);
    ob_start();
    var_dump($values);
    $status = ob_get_contents();
    ob_end_clean();
    
    dolog("Contents of \$asset: $status");

    # Lookup the asset. If it exists then update it. Otherwise, insert it.
    $sql = "SELECT COUNT(*) as c FROM asset WHERE drop_name = ? and name = ?";
    $arr = array($values['drop_name'],$values['name']);
    $r = DB::getInstance();

    $s = $r->prepare($sql)->
                execute($arr)->
                fetch();

    ob_start();
    var_dump($s);
    $status = ob_get_contents();
    ob_end_clean();
    
    dolog("Asset returned: $status");

    if ((int) $s['c'] > 0)
    {

        dolog("Found it. updating...");

        $sql = "UPDATE asset set `values` = ?, is_complete = 1 WHERE drop_name = ? AND name = ?";
        $arr = array($asset,$values['drop_name'],$values['name']);

    } else {

        dolog("Did not find it. Inserting...");
        $sql = "INSERT INTO asset (`created_at`, `name`, `drop_name`, `values`, `type`,`is_complete`) VALUES (?,?,?,?,?,1)";
        $arr = array(
          $values['created_at'],
          $values['name'],
          $values['drop_name'],
          $asset,
          $values['type']
        );
    }
    
    $r->prepare($sql)->execute($arr);

    return true;
}

function asset_destroyed($asset)
{
  $sql = "DELETE FROM asset WHERE drop_name = ? AND name = ?";
  $values = json_decode($asset,true);

  $arr = array($values['drop_name'],$values['name']);

  DB::getInstance()->prepare($sql)->execute($arr);
  
}
