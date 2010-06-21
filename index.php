<?php


include('lib/dropio-php/Dropio/Api.php');
include('config.inc.php');

//Please be sure to copy config.inc.php.sample to config.inc.php
//then add your own $API_KEY in that file
 
Dropio_Api::setKey($API_KEY);
$dropname = $_REQUEST['dropname'];

//Example of adding a note to a drop
//Dropio_Drop::load($dropname)->addNote('This is an example of the Drop.io PHP Library','Hello World');

$page = 1;

//Set the $dropname to the passed in parameter, or create a new drop with a random name
if(!empty($dropname)){
	$drop = Dropio_Drop::load($dropname);
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
if($_REQUEST['viewmode'] == 'permalink'){//it's a permalink, just get the requested asset
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
	
	<?php if($_REQUEST['viewmode'] != 'detailed'){  
			?>
		<script type="text/javascript" src="uploadify/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="uploadify/swfobject.js"></script>
		<script type="text/javascript" src="uploadify/jquery.uploadify.v2.1.0.min.js"></script>
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
	
	<?php } ?>
</head>
<body>

<?php 
if($mail_sent){
	echo "<h2>Mail sent</h2>";
}
/* 
####################################################################################### 
###Media rendering mode################################################################ 
#######################################################################################  */ ?>
<?php 
if (empty($_REQUEST["viewmode"]) || $_REQUEST["viewmode"] == 'media' || $_REQUEST["viewmode"] == 'permalink') 
{ ?>
<style type="text/css">
	body{background:url('images/fancybg.png') #dbdbdb repeat-x;}
	table{border:1px solid #aaaaaa;}
	table th{border-bottom:1px solid black;}
	table td{border-bottom:1px solid #cccccc;padding:10px;}
</style>
<script src='osflv/AC_RunActiveContent.js' language='javascript'></script>

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
								if($loc["name"] != "DropioS3"){ $ocurl .= "&location=" + $loc["name"]; } 
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
<div id="uploader">
	<h1>Upload</h1>
	<form action="<?php echo  Dropio_Api::UPLOAD_URL; ?>" enctype="multipart/form-data" method="post">
	
	<p><label for="file">Select File</label> : 
		<input type="file" name="file" id="file" /></p>
	<input type="hidden" name='api_key' value='<?php echo $API_KEY; ?>' />
	<input type="hidden" name='version' value='3.0' />
	<input type="hidden" name='drop_name' value='<?php echo $dropname; ?>' />
	<input type="hidden" name='redirect_to' value='<?php echo  "http://" . $_SERVER["HTTP_HOST"]  . $_SERVER["REQUEST_URI"]; ?>' />
	Output Locations: 
	<?php foreach ($output_locations as $ol){  ?> 
		<br />
		<input type="checkbox" name='output_location[<?php echo  $ol; ?>]' id="" value='<?php echo  $ol; ?>'  onclick="makeol()" <?php if ($ol == "DropioS3") {echo  "checked";}  ?>/>
		
		
		<label for="output_location[<?php echo  $ol; ?>]" ><?php echo  $ol; ?></label>
		
	<?php } ?>
	<input type="hidden" name='output_locations' value='DropioS3' id='olfield' />
<br />	<input type="submit" /></form>
<br />	<br />	<br />	<br />

</div>

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
	body{background:url('images/fancybg.png') #dbdbdb repeat-x;}
	table{border:1px solid #aaaaaa;}
	table th{border-bottom:1px solid black;}
	table td{border-bottom:1px solid #cccccc;padding:10px;}
</style>
<script src='osflv/AC_RunActiveContent.js' language='javascript'></script>

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
	
	if($a->type == 'image'){
		$message .= '<h3>Preview:</h3>';
		$message .= GetAssetPreview($a);
	}

	//send the email
	//echo 'sending to ' . $to;
	$mail_sent = @mail( $to, $subject, $message, $headers );
	return($mail_sent);
}
function GetAssetPreview($a){
	if ($a->type == "image"){
		foreach ($a->roles as $name=>$r) { 
			if ($r["name"] == "large_thumbnail"){ 
				if ($r["locations"][0]["status"] == "complete"){
					$preview = "<img style='width:240px;' src=\"". $r["locations"][0]["file_url"] . "\" alt='".htmlspecialchars($a->name)."'>";
					$preview .= "<br />Original width = " . $dimensions['width'] . ", height = " .$dimensions['height'];
				}
			}
			if ($r["name"] == "original_content"){
				$dimensions['width'] = $r["width"];
				$dimensions['height'] = $r["height"];
			} 
		}
	} elseif ($a->type == "audio"){
		if ($a->roles[0]["locations"][0]["status"] == "complete"){
			#$preview = '<embed type="application/x-shockwave-flash" src="http://www.google.com/reader/ui/3247397568-audio-player.swf?audioUrl='
			#$preview .= CGI::escape origfile
			#$preview .= '" width="400" height="27" allowscriptaccess="never" quality="best" bgcolor="#ffffff" wmode="window" flashvars="playerMode=embedded" />'

			$preview = '<object type="application/x-shockwave-flash" data="wpaudio/player.swf" id="';
			$preview .= 'ap-' + $a->name;
			$preview .= '" height="24" width="290">
			<param name="movie" value="wpaudio/player.swf">
			<param name="FlashVars" value="playerID=';
			$preview .= 'ap-' + $a->name;
			$preview .= '&soundFile=';
			$preview .= urlencode($origfile);
			$preview .= '">
			<param name="quality" value="high">
			<param name="menu" value="false">
			<param name="wmode" value="transparent">
			</object>';
			#$preview .= $a->inspect
		}
	}elseif ($a->type == "movie"){
		$flv = '';
		foreach ($a->roles as $name=>$r) {  
			if ($r["name"] == "web_preview") {
				if ($r["locations"][0]["status"] == "complete"){
					$flv = $r["locations"][0]["file_url"];
					$preview = "<object width='400' height='325' id='flvPlayer'>
					  <param name='allowFullScreen' value='true'>
					  <param name='movie' value='osflv/OSplayer.swf?movie=";
					$preview .= urlencode($flv);
					$preview .= "&btncolor=0x333333&accentcolor=0x31b8e9&txtcolor=0xdddddd&volume=30&previewimage=&autoload=off&vTitle=&showTitle=yes'>
					  <embed src='osflv/OSplayer.swf?movie=";
					$preview .=  urlencode($flv);
					$preview .= "&btncolor=0x333333&accentcolor=0x31b8e9&txtcolor=0xdddddd&volume=30&previewimage=&autoload=off&vTitle=&showTitle=yes' width='400' height='325' allowFullScreen='true' type='application/x-shockwave-flash'>
					 </object>";
				}
			}
		}
	}elseif ($a->type == "document"){
		foreach ($a->roles as $name=>$r) {  
			if ($r["name"] == "web_preview"){
				if ($r["locations"][0]["status"] == "complete"){
					$docurl = $r["locations"][0]["file_url"];
					$preview .= "<iframe style='width:600px;height:802px' frameborder='0' src=\"http://docs.google.com/viewer?embedded=true&url=";
					$preview .= urlencode($docurl);
					$preview .= "\"></iframe>";
				}
			}
		}
	}elseif ($a->type == "note"){
			$preview = $a->roles[0]["contents"];
			#echo print_r($a);
	}else{
		$preview = "<a href='". $origfile . "'><img src='images/downloaddisk.png' style='border:none' alt='download'/></a>";
		#$preview = h($a->inspect)
	}
	return $preview;
}
function GetAssetsByType($type = array("image", "movie", "audio", "document", "other", "note")){
	global $drop, $assets, $API_KEY, $dropname, $enabled_cdns, $assetCount;
	$page = 1;
	foreach ($assets as $name=>$a) {
			if(in_array($a->type, $type)){
			//echo "monkeypants";
			$origfile = "http://api.drop.io";
			$origfile .= "/drops/".$dropname."/assets/".$a->name."/download/original?api_key=".$API_KEY;
			$origfile .= "&version=3.0";
			unset($dimension);
			$dimension = Array();
			if ($a->roles[0]["locations"][0]["name"] != "DropioS3"){
				$origfile .= "&location=" + $a->roles[0]["locations"][0]["name"];
			}
			?>
			<tr>
				<td>
					<strong><?php echo  $a->title ?></strong>
					<?php 
					if(!empty($a->description)){
						echo  "<br />" . $a->description; 
				 	}
					?>
				</td>
				<td>
					<?php 
					$preview = GetAssetPreview($a);
					echo $preview; 
					?>
						<div id="emailthis-<?php echo $a->{$a->primary_key}; ?>" style="display:none;"><form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post"> 
	<input type="text" name="emailaddresses" /><br />
	<input type="hidden" name="assetid" value="<?php echo $a->{$a->primary_key};  ?>" />
	<input type="hidden" name="dropname" value="<?php echo $dropname; ?>" />
	<input type="hidden" name='viewmode' value='<?php echo $_REQUEST["viewmode"]; ?>' />
	<input type="hidden" name="action" value="emailthis" />
	<input type="submit" value="Send this asset" /></form></div>
					</td>
					<td><?php if ($a->type != "note") { ?><a href="<?php echo $origfile; ?>">Download File</a><?php } ?>
						<hr />	<a href="<?php echo $_SERVER['PHP_SELF'] . '?dropname='.$dropname.'&viewmode='.$_REQUEST['viewmode'].'&action=delete&assetid='.$a->{$a->primary_key} ?>" >delete asset</a><hr />	<a href="#" onclick="$('#emailthis-<?php echo $a->{$a->primary_key}; ?>').toggle(400);return false;" >Email asset</a>
					
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
	}elseif($type == "other"){
		return "Other Files";
	}
}
?>