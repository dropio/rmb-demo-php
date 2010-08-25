<?php

$o = array();

# loop over assets and break it out into types
foreach($assets as $a)
{
  $o[$a['type']][] = $a;
}

foreach ($o as $k => $v)
{
  include_once("list-$k.php");
}

