<?php

# Turn on all error reporting while we develop
error_reporting(E_ALL);

# Get API access
include('lib/dropio-php/Dropio/Api.php');

# Detech a mobile user
include('Mobile_Detect.php');

# Load the API keys and secrets
include('config.inc.php');

$detect = new Mobile_Detect();

$docroot = 'http://' . $_SERVER["SERVER_NAME"] . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/') + 1);

//Please be sure to copy config.inc.php.sample to config.inc.php
//then add your own $API_KEY in that file

# Set api key to be global for the life of this script
# should we change this to the define('API_KEY','xxx') constants?
Dropio_Api::setKey($API_KEY,$API_SECRET);

# Get the name of the "drop"
$dropname = $_REQUEST['dropname'];

//Example of adding a note to a drop
//Dropio_Drop::load($dropname)->addNote('This is an example of the Drop.io PHP Library','Hello World');

$page = 1;

//Set the $dropname to the passed in parameter, or create a new drop with a random name
if(!empty($dropname)){
	$drop = Dropio_Drop::load($dropname);
}else if($_REQUEST['newdrop']){
	$drop = Dropio_Drop::instance($_REQUEST['newdrop'])->save();
	$dropname = $drop->name;
}else{
	$drop = Dropio_Drop::instance()->save();
	$dropname = $drop->name;
}

//Set the CDNs we'd like to provide. These will be appended to download urls with the "via" parameter
$enabled_cdns = array('akamai','voxel','limelight');

//Set the output locations possible for uploads. You can create new output locations under the API tab in your account
$output_locations = array('DropioS3');

//Define types available
$alltypes = array("image", "movie", "audio", "document", "other", "note");

//If you want to examine the full output of the drop object, uncomment this line
//echo print_r($drop);

//Fetch all assets in the drop into a global $assets variable
$assets = array();
$assetCount = array();
if($_REQUEST['viewmode'] == 'permalink' || $_REQUEST["action"] == "updateasset"){//it's a permalink, just get the requested asset
	$assets[] = $drop->getAsset($_REQUEST['assetid']);
}else{
	while ( ($assetsIn = $drop->getAssets($page)) && $assetsIn->getCount()) {
		foreach ($assetsIn as $assetIn){
			$assets[] = $assetIn;
			$assetCount[$assetIn->type]++;
		}
		$page++;
	}
}

//////////////////////
//Asset deletion
if($_REQUEST["action"] == "delete" && $_REQUEST["assetid"]){
 	//iterate through assets
	$counter = 0;
 	foreach($assets as $a){
		if($a->{$a->primary_key} == $_REQUEST["assetid"]){
			$a->delete();
			//also remove that asset from the local array
			unset($assets[$counter]);
		}
		$counter++;
	}
}else if($_REQUEST["action"] == "updateasset" && $_REQUEST["assetid"]){
 	//iterate through assets
	$updated = '';
 	foreach($assets as $a){
		if($a->{$a->primary_key} == $_REQUEST["assetid"]){
			//check if data is safe
			if(json_decode(stripslashes($_REQUEST["metadata"]))){
				//encode the json data with htmlspecialchars to avoid filtering on the drop.io side
				$a->description = htmlspecialchars(stripslashes($_REQUEST["metadata"]));
				$updated = $a->save();
			}
		}
	}
	//redirect back to this page after updating
	//header("Location:http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?viewmode=".$_REQUEST["viewmode"]."&dropname=". $dropname);
	die(json_encode($a));
}else if($_REQUEST["action"] == "emailthis" && $_REQUEST["assetid"]){
 	//iterate through assets
	$counter = 0;
 	foreach($assets as $a){
		if($a->{$a->primary_key} == $_REQUEST["assetid"]){
			$mail_sent = SendAssetEmail($a, $_REQUEST["emailaddresses"]);
			//echo 'sending asset #' . $counter;
			break;
		}
		$counter++;
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
 
<head> 
	<title>Drop.io Rich Media Backbone Example</title>
	<style type="text/css">
		#assets{
			
		}
		body {
			background:#ddddff;
			margin:20px;
			padding:0;
			font-size:11px;
			font-family:sans-serif;
		}
		
		
	
	</style>
	
	<?php
	/* 
		####################################################################################### 
		### Uploadify uploader script (for all modes but detailed) ############################ 
		#######################################################################################  */ 
	 if($_REQUEST['viewmode'] != 'detailed'){  
			?>
		<script type="text/javascript" src="<?php echo $docroot; ?>uploadify/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="<?php echo $docroot; ?>uploadify/swfobject.js"></script>
		<script type="text/javascript" src="<?php echo $docroot; ?>uploadify/jquery.uploadify.v2.1.0.min.js"></script>
		<link rel="stylesheet" type="text/css" media="screen, projection" href="uploadify/uploadify.css" />
	
		<script type="text/javascript">// <![CDATA[
		$(document).ready(function() {
		$('#file').uploadify({
		'uploader'  : 'uploadify/uploadify.swf',
		'script'    : '<?php echo Dropio_Api::UPLOAD_URL; ?>',
		'multi'    : true,
		'scriptData': {'api_key': '<?php echo $API_KEY; ?>', 'version':'3.0','drop_name': '<?php echo $dropname; ?>'},
		'cancelImg' : 'uploadify/cancel.png',
		'auto'      : true,
		'onAllComplete' : function(){setTimeout(window.location = '<?php echo "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?viewmode=".$_REQUEST["viewmode"]."&dropname=". $dropname; ?>',3000);}, 
		'folder'    : '/uploads'
		});
		});
		// ]]></script>
		
		
	
	<?php 
	/* 
		####################################################################################### 
		### Audio player (for all modes but detailed)              ############################ 
		#######################################################################################  */ ?>
		<script type="text/javascript" src="<?php echo $docroot; ?>audio-player/audio-player.js"></script>
		<script type="text/javascript">  
            AudioPlayer.setup("audio-player/player.swf", {  
                width: 290,
				transparentpagebg: "yes",
				checkpolicy:"yes"
				
            });  
        </script>
	<?php 
	/* 
		####################################################################################### 
		### HTML5 video player (for all modes but detailed)        ############################ 
		#######################################################################################  */ ?>
		<link rel="stylesheet" href="<?php echo $docroot; ?>video-js/video-js.css" type="text/css" media="screen" title="Video JS" charset="utf-8"/>
		<script src="<?php echo $docroot; ?>video-js/video.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
			// If using jQuery
		     $(function(){
		       VideoJS.setup();
		     })
		</script>
	<?php 
	/* 
		####################################################################################### 
		### JSON metadata editor (for all modes but detailed)      ############################ 
		#######################################################################################  */ ?>
		<link rel="stylesheet" type="text/css" href="jsoneditor/jsoneditor.css" />
		
		<script type="text/javascript" src="<?php echo $docroot; ?>jsoneditor/jquery.json-2.2.min.js"></script>
		<script type="text/javascript" src="<?php echo $docroot; ?>jsoneditor/jquery.jsoneditor.js"></script> 
		
		<script type="text/javascript">
			function updateAsset(assetid, data){
				$('#wrap_je_' + assetid).toggle(400);
				jsondata = $.toJSON(data);
				//alert('updating the asset ' + assetid + ' with the data ' + jsondata);
				dataobj = {metadata:jsondata,
					assetid:assetid,
					action:'updateasset',
					dropname:'<?php echo $dropname; ?>',
					viewmode:'<?php echo $_REQUEST["viewmode"]; ?>'} ;
				//console.log(dataobj);
				$.ajax({type:'POST',data:dataobj
						,success: function(data) {
							//console.log(data);
				    		alert('Saved metadata for ' + assetid);
				  		},error: function(data) {
							//console.log(data);
				    		alert('Error on ' + assetid);
				  		}});
			}
		</script>
	<?php } ?>
</head>
<body>
<?php 

if($mail_sent){
	echo "<h2>Mail sent</h2>";
}else if($updated){
	echo "<h2>asset updated</h2>";
}
/* 
####################################################################################### 
###Media rendering mode################################################################ 
#######################################################################################  */ ?>
<?php 
if (empty($_REQUEST["viewmode"]) || $_REQUEST["viewmode"] == 'media' || $_REQUEST["viewmode"] == 'permalink') 
{ ?>
<style type="text/css">
	body{background:url('<?php echo $docroot; ?>images/fancybg.png') #dbdbdb repeat-x;}
	table{border:1px solid #aaaaaa;}
	table th{border-bottom:1px solid black;}
	table td{border-bottom:1px solid #cccccc;padding:10px;}
	.metadata{background:#fff;border:1px solid #aaa;margin:4px;padding:4px;}
	
</style>
<script src='<?php echo $docroot; ?>osflv/AC_RunActiveContent.js' language='javascript'></script>

<div id="assets">
	<?php if ($_REQUEST['viewmode'] == 'permalink') { ?>
			<h2 style="font-size:24px">Viewing the asset <?php echo  $_REQUEST['assetid'] ?> the drop: <?php echo  $dropname ?> </h2>
			<h4><a href="<?php echo $_SERVER['PHP_SELF'] . '?viewmode=media&dropname='.$dropname; ?>">&laquo;View all assets in this drop</a></h4>
	<?php }else{?>
			<h2 style="font-size:24px">Viewing the items in the drop: <?php echo  $dropname ?> </h2>
			<h4>Switch to
			<a href="<?php echo $_SERVER['PHP_SELF'] . '?viewmode=detailed&dropname='.$dropname; ?>">detailed view</a> to see more information about these assets, or view them <a href="<?php echo $_SERVER['PHP_SELF'] . '?viewmode=sorted&dropname='.$dropname; ?>">sorted by type</a></h4>
	<?php } ?>

		<br />
		<table><tr><th width="250">Title &amp; Description</th><th width="400">Preview</th><th width="50">Links</th></tr> 
		<?php 
			//passing no types will return all of them
			GetAssetsByType();
		?>
		</table>
	<br /><br />

</div>
<script type="text/javascript">
	function makeol(){
		//alert('test');
		var chks = $$("input");
		var olval = [];
		chks.each( function( element ) {
			if(element.type == 'checkbox' && element.checked){
				//alert(element.value);
				olval.push(element.value);
			} 
		});
		$("olfield").value = olval.join(",");
	}
</script>
<div id="uploader" style="background:#ffffff;-moz-border-radius:20px;-webkit-border-radius:20px;width:660px;padding:10px 20px 20px 20px;margin-top:30px">
	<h1>Upload a new file to this drop</h1>
	
	<input id="file" name="file" type="file" />
	
</div>

<?php
/* 
####################################################################################### 
### Detailed rendering mode ########################################################### 
#######################################################################################  */ ?>

<?php } else if($_REQUEST['viewmode'] == 'detailed'){  
	?>


<div id="assets">
	<h2>Examining the drop <?php echo  $dropname; ?></h2>
	
	<h4>Switch to
	<a href="<?php echo $_SERVER['PHP_SELF'] . '?dropname='.$dropname.'&viewmode=media'; ?>">media view</a> to see previews of these assets.</h4>

	<br />
	There are <?php echo  $drop->values['asset_count']; ?> items in this drop
		<br />

		<ul>
		<?php 
			foreach ($assets as $name=>$a) { ?>
			<li>
			<h3><?php echo  $a->name;  ?></h3>
			<ul>
			<?php  foreach ($a->roles as $name=>$r) { ?>
				<li>Role: <?php echo  ($r["name"]); ?>
					<ul>
					<?php foreach ($r["locations"] as $name=>$loc) { ?>
						<li>Location: <?php echo $loc["name"]; ?></li>
						<?php if ($r["name"] == "original_content") { ?>
							<?php 
								$ocurl = "http://api.drop.io/drops/".$dropname."/assets/".$a->name."/download/original?api_key=".$API_KEY."&version=3.0";
								if($loc["name"] != "DropioS3"){ $ocurl .= "&location=" . $loc["name"]; } 
							?>
							<ul><li><a href="<?php echo $ocurl; ?>">
							Direct Download</a> or via 
							<?php
								unset($cdnout);
								foreach ($enabled_cdns as $cdn){
									$cdnout[] = '<a href="'. $ocurl . '&via=' . $cdn . '">' . $cdn . '</a>';
								}
							?>
							<?php echo join($cdnout, ', '); ?>
							</li></ul>	
						<?php } else { ?>
							<?php if($loc["status"] == "complete") { ?>
								<ul><li><a href="<?php echo  $loc["file_url"] ?>">Direct Download</a> or via 
								<?php
									unset($cdnout);
								 	foreach ($enabled_cdns as $cdn){
										$cdnout[] = '<a href="'. $loc["file_url"] . '?via=' . $cdn . '">' . $cdn . '</a>';
									}
							
									 ?>
								<?php echo join($cdnout, ', ') ?>
								</li></ul>
							<?php } else { ?>
									<ul><li><?php echo $loc["status"]?></li></ul>
							<?php } ?>
						<?php } ?>
					<?php } ?>
					</ul>
				</li>
			<?php } 
			   ?>
			</ul>
			</li>
		<?php  } 
		?> </ul>
		
	<br /><br />

</div>
<script type="text/javascript">
	function makeol(){
		//alert('test');
		var chks = $$("input");
		var olval = [];
		chks.each( function( element ) {
			if(element.type == 'checkbox' && element.checked){
				//alert(element.value);
				olval.push(element.value);
			} 
		});
		$("olfield").value = olval.join(",");
	}
</script>

<!-- BEGIN UPLOADER -->
<div id="uploader">
  <h1>Upload</h1>
	<form action="<?php echo Dropio_Api::UPLOAD_URL; ?>" enctype="multipart/form-data" method="post">
	
	<p><label for="file">Select File</label> : 
  <input type="file" name="file" id="file" /></p>
	<input type="hidden" name='api_key' value='<?php echo $API_KEY; ?>' />

	<?php if (isset($API_SECRET)): ?>
    <input type="hidden" name="api_signature" value="<?php echo Dropio_Drop::getSignature() ?>" />
    <input type="hidden" name="timestamp" value="<?php echo Dropio_Drop::getSignature() ?>" />
  <?php endif ?>

  <input type="hidden" name='version' value='3.0' />
	<input type="hidden" name='drop_name' value='<?php echo $dropname; ?>' />
	<input type="hidden" name='redirect_to' value='<?php echo  "http://" . $_SERVER["HTTP_HOST"]  . $_SERVER["REQUEST_URI"]; ?>' />

<br />	<input type="submit" /></form>
<br />	<br />	<br />	<br />

</div>
<!-- END UPLOADER -->

<br />

	<br />
	<?php /* 
	####################################################################################### 
	###Sorted (grouped) rendering mode##################################################### 
	#######################################################################################  */ ?>
<?php 
} else if ($_REQUEST["viewmode"] == 'sorted') 
{ ?>
<style type="text/css">
	body{background:url('<?php echo $docroot; ?>images/fancybg.png') #dbdbdb repeat-x;}
	table{border:1px solid #aaaaaa;}
	table th{border-bottom:1px solid black;}
	table td{border-bottom:1px solid #cccccc;padding:10px;}
	.metadata{background:#fff;border:1px solid #aaa;margin:4px;padding:4px;}
</style>
<script src='<?php echo $docroot; ?>osflv/AC_RunActiveContent.js' language='javascript'></script>

<div id="assets">
	<h2 style="font-size:24px">Viewing assets in the drop: <?php echo  $dropname ?> </h2>
	<h4>Switch to
	<a href="<?php echo $_SERVER['PHP_SELF'] . '?viewmode=detailed&dropname='.$dropname; ?>">detailed view</a> to see more information about these assets, or <a href="<?php echo $_SERVER['PHP_SELF'] . '?viewmode=media&dropname='.$dropname; ?>">sorted by date</a>.</h4>
		<br />
		<?php 
		foreach($alltypes as $type){ 
			if($assetCount[$type]) { ?> 
				<h2><?php echo PluralizeType($type); ?></h2>
				<table><tr><th width="250">Title &amp; Description</th><th width="400">Preview</th><th width="50">Links</th></tr> 
				<?php 
					GetAssetsByType(array($type));
				?>
				</table>
				<?php 
			}
		} ?>
	<br /><br />

</div>
<div id="uploader" style="background:#ffffff;-moz-border-radius:20px;-webkit-border-radius:20px;width:660px;padding:10px 20px 20px 20px;margin-top:30px">
	<h1>Upload a new file to this drop</h1>
	
	<input id="file" name="file" type="file" />
	
</div>



<?php } ?>
</body></html>
<?php
function SendAssetEmail($a, $emails){
	global $drop, $assets, $API_KEY, $dropname, $enabled_cdns, $assetCount;
	//define the receiver of the email
	$to = $emails;
	//define the subject of the email
	$subject = 'You\'ve been sent a file using the Drop.io RMB PHP demo'; 
	//define the headers we want passed. Note that they are separated with \r\n
	$headers = "From: eric@dropio.com\r\nReply-To: eric@dropio.com\r\n";
	//add boundary string and mime type specification
	// Always set content-type when sending HTML email
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	//define the body of the message.
	$message = '<h2>You\'ve been sent the file '. $a->{$a->primary_key} .'</h2>
	<p>You can check this file out here:<br /><a href="http://' . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . '?viewmode=permalink&assetid='.$a->{$a->primary_key}.'&dropname='. $dropname .'">http://' . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . '?viewmode=permalink&assetid='.$a->{$a->primary_key}.'&dropname='. $dropname .'</a></p> 
	';
	
	if(in_array($a->type, array('image','note', 'link'))){
		$message .= '<h3>Preview:</h3>';
		$message .= GetAssetPreview($a);
	}

	//send the email
	//echo 'sending to ' . $to;
	$mail_sent = @mail( $to, $subject, $message, $headers );
	return($mail_sent);
}
function GetAssetPreview($a){
	global $detect, $docroot;
	if ($a->type == "image"){
		//first, get some info from the original content
		foreach ($a->roles as $name=>$r) { 
			if ($r["name"] == "original_content"){
				$dimensions['width'] = $r["width"];
				$dimensions['height'] = $r["height"];
			}
		}
		//now, display the image
		foreach ($a->roles as $name=>$r) { 
			if ($r["name"] == "large_thumbnail"){ 
				if ($r["locations"][0]["status"] == "complete"){
					$preview = "<img style='width:";
					$preview .= $r["width"] / 2;
					$preview .= "px;height:";
					$preview .= $r["height"] / 2;
					$preview .= "px;' src=\"";
					$preview .= $r["locations"][0]["file_url"];
					$preview .= "\" alt='";
					$preview .= htmlspecialchars($a->name);
					$preview .= "'>";
					//If you wanted to echo the original width and height of the uploaded file, you can do that here:
					//$preview .= "<br />Original width = " . $dimensions['width'] . ", height = " .$dimensions['height'];
				}
			}
		}
	} elseif ($a->type == "audio"){
		foreach ($a->roles as $name=>$r) {  
			if ($r["name"] == "web_preview") {
				if ($r["locations"][0]["status"] == "complete"){
					//play using HTML5 for mobile (webkit and iphone/ipad support)
					if($detect->isMobile()){
						$preview ='<audio src="'.$r["locations"][0]["file_url"].'" controls autobuffer></audio>';
					}else{
					//use an open source flash player for regular web browsers
						$preview ='<p id="ap-'.$a->name.'"></p>  
				        <script type="text/javascript">  
				        AudioPlayer.embed("ap-'.$a->name.'", 
							{
								soundFile: "'.urlencode($r["locations"][0]["file_url"]).'",
								titles: "'.$a->title.'"
							});  
				        </script>';
					}
				}
			}
		}
	}elseif ($a->type == "movie"){
		$movie = '';
		//first get the poster image
		$poster = '';
		foreach ($a->roles as $name=>$r) { 
			if ($r["name"] == "large_thumbnail"){
				if ($r["locations"][0]["status"] == "complete"){
					$poster = $r["locations"][0]["file_url"];
				}
			}
		}
		//then get the web-friendly h.264 m4v file and wrap it in an HTML5 player with Flash fallback
		foreach ($a->roles as $name=>$r) { 
			if ($r["name"] == "web_preview") {
				if ($r["locations"][0]["status"] == "complete"){
					$movie = $r["locations"][0]["file_url"];
					$preview = '
					<!-- Begin VideoJS -->
					  <div class="video-js-box">
					    <!-- Using the Video for Everybody Embed Code http://camendesign.com/code/video_for_everybody -->
					    <video class="video-js" width="400" height="325" poster="'.$poster.'" controls preload>
					      <source src="'.$movie.'" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>';
							$preview .= "<object class='vjs-flash-fallback' width='400' height='325'>
							  <param name='allowFullScreen' value='true'>
							  <param name='movie' value='".$docroot."osflv/OSplayer.swf?movie=";
							$preview .= urlencode($movie);
							$preview .= "&btncolor=0x333333&accentcolor=0x31b8e9&txtcolor=0xdddddd&volume=30";
							$preview .= "&previewimage=" . urlencode($poster);
							$preview .= "&autoload=off&vTitle=".urlencode($a->title)."&showTitle=yes'>
							  <embed src='" . $docroot . "osflv/OSplayer.swf?movie=";
							$preview .=  urlencode($movie);
							$preview .= "&btncolor=0x333333&accentcolor=0x31b8e9&txtcolor=0xdddddd&volume=30";
							$preview .= "&previewimage=" . urlencode($poster);
							$preview .= "&autoload=off&vTitle=".urlencode($a->title)."&showTitle=yes' width='400' height='325' allowFullScreen='true' type='application/x-shockwave-flash'>
							 </object>";
					    $preview .= '</video>
					    <p class="vjs-no-video"></p>
					  </div>
					  <!-- End VideoJS -->';
					 //$preview .= "width = " . print_r($r);
					
				}
			}
		}
	}elseif ($a->type == "document"){
		foreach ($a->roles as $name=>$r) {  
			if ($r["name"] == "web_preview"){
				if ($r["locations"][0]["status"] == "complete"){
					$docurl = $r["locations"][0]["file_url"];
					if(!$detect->isMobile()){
						$preview .= "<iframe style='width:600px;height:400px' frameborder='0' src=\"http://docs.google.com/viewer?embedded=true&url=";
						$preview .= urlencode($docurl);
						$preview .= "\"></iframe>";
					}else{
						$preview = '<object data="'.$docurl.'" type="application/pdf" width="500" height="375" />';
					}
				}
			}
		}
	}elseif ($a->type == "link"){
		$preview = '<a href="'.$a->url.'">' . $a->url . '</a>';
	}elseif ($a->type == "note"){
		$preview = $a->roles[0]["contents"];
	}else{
		$preview = "<a href='". GetOriginalFileUrl($a) . "'><img src=' " .$docroot . "images/downloaddisk.png' style='border:none' alt='download'/></a>";
		#$preview = h($a->inspect)
	}
	return $preview;
}
function GetOriginalFileUrl($a){
	global $dropname, $API_KEY;
	$origfile = "http://api.drop.io";
	$origfile .= "/drops/".$dropname."/assets/".$a->name."/download/original?api_key=".$API_KEY;
	$origfile .= "&version=3.0";
	if ($a->roles[0]["locations"][0]["name"] != "DropioS3"){
		$origfile .= "&location=" . $a->roles[0]["locations"][0]["name"];
	}
	return $origfile;
}
function GetAssetsByType($type = array("image", "movie", "audio", "document", "other", "note", "link")){
	global $drop, $assets, $API_KEY, $dropname, $enabled_cdns, $assetCount;
	$page = 1;
	foreach ($assets as $name=>$a) {
			if(in_array($a->type, $type)){
			//echo "monkeypants";
			$origfile = GetOriginalFileUrl($a);
			unset($dimension);
			$dimension = Array();
			
			?>
			<tr>
				<td>
					<strong><?php echo  $a->title ?></strong>
					<?php
					$description = $a->description;
					//echo var_dump($description);
					$data = '{}';
					if(json_decode(stripslashes(htmlspecialchars_decode($description)))){
						//we had decodable data in the description. It's metadata!
						$data = stripslashes(htmlspecialchars_decode($description));
				 	}else if(!empty($description)){
						//just text in the description. Display it.
						echo "<br />" . $description;
					}
					$metadata  = "<div class='metadata' id='wrap_je_".$a->name."' style='display:none;'>";
					$metadata .= "<div class='metadata' id='je_".$a->name."' '></div>";
					$metadata .= "<script type='text/javascript'>$(function(){";
					$metadata .= "je=$('#je_".$a->name."');";
					$metadata .= "je.jsoneditor('init', {
							root:'metadata',
							data:" . $data . "});});";
					$metadata .= "</script>";
					$metadata .= '<input type="button" value="save"';
					$metadata .= " onclick=\"updateAsset('".$a->name."',$('#je_".$a->name."').jsoneditor('getjson'));\" />";
					$metadata .= "</div>";
					
					?>
				</td>
				<td>
					<?php 
					$preview = GetAssetPreview($a);
					//$preview .= 'Embed Code: <textarea rows="2" columns="60">' . htmlspecialchars(GetAssetPreview($a)) . '</textarea>';
					echo $preview; 
					?>
						<div id="emailthis-<?php echo $a->{$a->primary_key}; ?>" style="display:none;"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"> 
	<input type="text" name="emailaddresses" /><br />
	<input type="hidden" name="assetid" value="<?php echo $a->{$a->primary_key};  ?>" />
	<input type="hidden" name="dropname" value="<?php echo $dropname; ?>" />
	<input type="hidden" name='viewmode' value='<?php echo $_REQUEST["viewmode"]; ?>' />
	<input type="hidden" name="action" value="emailthis" />
	<input type="submit" value="Send this asset" /></form></div>
					<?php if ($metadata){echo $metadata;}?>
					</td>
					<td><?php if ($a->type != "note") { ?><a href="<?php echo $origfile; ?>">Download File</a><?php } ?>
						<hr />	
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?dropname='.$dropname.'&viewmode='.$_REQUEST['viewmode'].'&action=delete&assetid='.$a->{$a->primary_key} ?>" >delete asset</a><hr />	
						<a href="#" onclick="$('#emailthis-<?php echo $a->{$a->primary_key}; ?>').toggle(400);return false;" >Email asset</a><hr />
						<a href="#" onclick="$('#wrap_je_<?php echo $a->{$a->primary_key}; ?>').toggle(400);return false;">edit metadata</a>
					
					</td>
				</tr>
					<?php
		}
		
	}
			
	  
}
function PluralizeType($type){
	if($type == "image"){
		return "Images";
	}elseif($type == "movie"){
		return "Movies";
	}elseif($type == "audio"){
		return "Audio";
	}elseif($type == "document"){
		return "Documents";
	}elseif($type == "note"){
		return "Notes";
	}elseif($type == "link"){
		return "Links";
	}elseif($type == "other"){
		return "Other Files";
	}
}
?>