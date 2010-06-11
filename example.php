<?php


include('lib/dropio-php/Dropio/Api.php');

//Visit http://backbone.drop.io to apply for a Rich Media Backbone API key
$API_KEY = 'YOUR_API_KEY';

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

//If you want to examine the full output of the drop object, uncomment this line
//echo print_r($drop);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
 
<head> 
	<title>Dropio API demo</title>
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
	'onAllComplete' : function(){setTimeout(window.location.reload(),3000);}, 
	'folder'    : '/uploads'
	});
	});
	// ]]></script>
</head>
<body>

<?php if(empty($_REQUEST['viewmode']) || $_REQUEST['viewmode'] == 'detailed'){  
	?>


<div id="assets">
	<h2>Examining the drop <?php echo  $dropname; ?></h2>
	
	<h4>Switch to
	<a href="<?php echo $_SERVER['PHP_SELF'] . '?dropname='.$dropname.'&viewmode=media'; ?>">media view</a> to see these assets displayed inline</h4>

	<br />
	There are <?php echo  $drop->values['asset_count']; ?> assets in this drop
		<br />

		<ul>
		<?php while ( ($assets = $drop->getAssets($page)) && $assets->getCount()) {
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
			  
			$page++; ?>
			</ul>
			</li>
		<?php } } 
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
	<h1>File Upload</h1>
	<form action="<?php echo  Dropio_Api::UPLOAD_URL; ?>" enctype="multipart/form-data" method="post">
	
	<p><label for="file">Select File</label> : 
		<input type="file" name="file" id="fileraw" /></p>
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
###Media rendering mode################################################################ 
#######################################################################################  */ ?>
<?php 
} elseif ($_REQUEST["viewmode"] == 'media') 
{ ?>
<style type="text/css">
	body{background:url('images/fancybg.png') #dbdbdb repeat-x;}
	table{border:1px solid #aaaaaa;}
	table th{border-bottom:1px solid black;}
	table td{border-bottom:1px solid #cccccc;padding:10px;}
</style>
<script src='osflv/AC_RunActiveContent.js' language='javascript'></script>

<div id="assets">
	<h2 style="font-size:24px">Viewing the contents of "<?php echo  $dropname ?>"</h2>
	<h4>Switch to
	<a href="<?php echo $_SERVER['PHP_SELF'] . '?dropname='.$dropname; ?>">detailed view</a> to view information about these assets</h4>
		<br />
		<table><tr><th width="250">Title &amp; Description</th><th width="400">Preview</th><th width="50">Original file</th></tr> 
		<?php 
			while ( ($assets = $drop->getAssets($page)) && $assets->getCount()) {
		  		foreach ($assets as $name=>$a) {
			$origfile = "http://api.drop.io";
			$origfile .= "/drops/".$dropname."/assets/".$a->name."/download/original?api_key=".$API_KEY;
			$origfile .= "&version=3.0";
			if ($a->roles[0]["locations"][0]["name"] != "DropioS3"){
				$origfile .= "&location=" + $a->roles[0]["locations"][0]["name"];
			}
			?>
			<tr>
				<td>
					<strong><?php echo  $a->title ?></strong>
					<?php 
					if(!empty($a->description)){ 	
						echo  "<br />" + $a->description; 
					} 
					?>
				</td>
				<td>
					<?php 
					$preview = '';
					if ($a->type == "image"){
						foreach ($a->roles as $name=>$r) { 
							if ($r["name"] == "thumbnail"){ 
								if ($r["locations"][0]["status"] == "complete"){
									$preview = "<img src=\"". $r["locations"][0]["file_url"] . "\" alt='".htmlspecialchars($a->name)."'>";
								}
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
									$preview .= "<iframe style='width:600px;height:300px' frameborder='0' src=\"http://docs.google.com/viewer?embedded=true&url=";
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
					echo $preview; ?>
				</td>
				<td><?php if ($a->type != "note") { ?><a href="<?php echo $origfile; ?>">link</a><?php } ?></td>
			</tr>
		<?php } 
		
		  
			$page++;
		}
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

<?php } ?>
</body></html>