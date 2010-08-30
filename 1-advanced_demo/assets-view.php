<?php

include_once '_bootstrap.php';
include_once 'templates/_helper.php';
include_once '../lib/dropio-php/Dropio/Drop.php';

$type = (isset($_GET['type'])) ? $_GET['type'] : 'all';

if($type == 'all')
{
    $assets = DB::getInstance()->prepare("SELECT * FROM asset WHERE drop_name = ? order by type")
                ->execute(array($_GET['drop_name']))
                ->fetchAll();
} else {
    $assets = DB::getInstance()->prepare("SELECT * FROM asset WHERE drop_name = ? and type = ?")
                ->execute(array($_GET['drop_name'], $type))
                ->fetchAll();
}

$arr = array();
foreach ($assets as $a)
{
    $arr[$a['type']][] = $a;
}


$uploadify_options = array( 'pingback_url' => "{$docroot}pingback.php");

?>
<html>

<head>
<title>Viewing all assets for drop '<?php echo $_GET['drop_name']?></title>
<link rel="stylesheet" type="text/css" href="../css/main.css"/>

<!-- load jQuery -->
<script type="text/javascript" src="../utils/uploadify/jquery-1.3.2.min.js"></script>

<!-- Fancybox -->
<script type="text/javascript" src="../utils/fancybox/jquery.fancybox-1.3.1.js"></script>
<link rel="stylesheet" type="text/css" href="../utils/fancybox/jquery.fancybox-1.3.1.css"/>

<!-- Javascript RMB API and streamer -->
<script type="text/javascript" language="javascript" src="js/RichMediaBackboneAPI.js"></script>
<script type="text/javascript" charset="utf-8" src="http://drop.io/javascripts/streamer.js"></script>

<!-- The Audio player -->
<script type="text/javascript" src="../utils/wpaudio/audio-player.js"></script>

<script type="text/javascript">
AudioPlayer.setup("http://dropio.m3b.net/utils/wpaudio/player.swf", {
  width: 290
});

var api = new DropioApiClient("<?php echo $API_KEY ?>","http://dropio.m3b.net/DropioJSClientXDReceiver.html");
var params = { name : "<?php echo $_GET['drop_name'] ?>" };

var callback = function(response, status) {
    alert(status); // true, if successful
    alert(response); // JSON response object about the drop named "foobar"
};

</script>


    </head>
    <body>
        <div id="container">
        <?php include_once('_slot_drops.php'); ?>
        
        <h4><?php echo $_GET['drop_name']?> &gt; <?php echo $type ?></h4>

        <hr/>
        <a href="javascript:api.getDrop({ name : "<?php echo $_GET['drop_name']?>" }, callback ); ?>">Empty Drop</a>
        <hr/>
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=all">All</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=image">Images</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=movie">Movies</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=audio">Audio</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=document">Documents</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=notes">Notes</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=link">Links</a> |
        <a href="assets-view.php?drop_name=<?php echo $_GET['drop_name']?>&type=other">Other</a>
        <div style="float: right">
            <?php include_once('_slot_uploadify_form.php'); ?>
        </div>
        <hr/>
        <?php foreach ($arr as $k=>$v):   ?>
        <div style="clear: both" id="<?php echo $k ?>-container">
            <h3><?php echo $k?></h3>
            <?php foreach($v as $ass): ?>
            <div class="thumb"><?php $func = "show_$k";echo $func($ass); ?></div>
            <?php endforeach ?>
        </div>
        <?php endforeach ?>
    </div>
    </body>
</html>