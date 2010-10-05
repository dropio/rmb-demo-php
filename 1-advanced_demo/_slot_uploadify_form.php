<?php
  $drop_name = $_GET['drop_name'];
  try {
    $drop = Dropio_Drop::getInstance($API_KEY, $API_SECRET)->load($drop_name);
    $chatPass = $drop->getChatPassword();
  } catch (Exception $e) {
    echo '</div><h1>', $e->getMessage(), '</h1>';
    echo '<p><a id="deletedrop" href="drop-delete_drop.php?drop_name=', $_GET['drop_name'];
    echo '">Delete this drop from your database</a></p></body></html>';
    die();
  }
?>

<script type="text/javascript" src="../utils/uploadify/swfobject.js"></script>
<script type="text/javascript" src="../utils/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="../utils/uploadify/uploadify.css" />

<script type="text/javascript">// <![CDATA[

function updateAsset(asset) {
    // Shortcut to the objects we want
	console.log(asset);
    var type = asset.type; // image, movie, document, etc
	var location;
    var myLink = document.getElementById(asset.name);
    // iterate over the available roles (there may only be 1, in the case of stream mesages)
	$.each(asset.roles, function(index, role) { 
		//grab the first available location
		location = role.locations.location
		//Handle this update based on the type of asset
    	switch (type)
	    {
	      case 'image' :
	        if (role.name == 'original_content' && asset.roles.length == 1) {
		 		myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
				$(myLink).after(role.filename);
		        $(myLink).fancybox({'type':'image'});
				myLink.innerHTML = '<img src="images/spinner.gif" alt="Conversion processing"/>';
			}else if (role.name == 'custom_small_thumb' && location.status == "complete") {
	          myLink.innerHTML = '<img src="' + location.file_url + '"/>';
	        }else if (role.name == 'custom_large_thumb' && location.status == "complete") {
	          $(myLink).attr('href',location.file_url);
	        }
	        
			break;

	      case 'movie'  :
	        if (role.name == 'original_content' && asset.roles.length == 1){
				myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
				$(myLink).after(role.filename);
		        $(myLink).fancybox({'type':'movie'});
				myLink.innerHTML = '<img src="images/spinner.gif" alt="Conversion processing"/>';
			}else if (role.name == 'custom_movie_thumb') {
	          myLink.innerHTML = '<img src="' + location.file_url + '"/>';
	        }else if (role.name == 'custom_mp4') {
	          myLink.setAttribute('href',location.file_url);
	          $(myLink).each(function(){
	            $(this).fancybox({
			        'padding'   : 0,
			        'autoScale' : true,
			        'type'      : 'iframe',
			        'width'     : 660,
			        'height'    : 540,
			        'href'      : '<?php echo $docroot ?>/1-advanced_demo/_video_player.php?file=' + $(this).attr('href') + '&poster=' + $(this).attr('poster')
			      });
	          });
	        }else if (role.name == 'custom_movie_poster') {
	          document.getElementById(asset.name).setAttribute('poster',location.file_url);
				$(this).fancybox({
			        'padding'   : 0,
			        'autoScale' : true,
			        'type'      : 'iframe',
			        'width'     : 660,
			        'height'    : 540,
			        'href'      : '<?php echo $docroot ?>/1-advanced_demo/_video_player.php?file=' + $(this).attr('href') + '&poster=' + $(this).attr('poster')
			      });
	        }
	        break;

	      case 'document' :
	        if (role.name == 'original_content' && asset.roles.length == 1 && role.filename.substr(role.filename.lastIndexOf(".")+1, 3).toLowerCase() != 'pdf'){
				myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
				$(myLink).after(role.filename);
				myLink.innerHTML = '<img src="images/spinner.gif" alt="Conversion processing"/>';
			}else if (role.name == 'custom_pdf' || role.name == 'original_content' && role.filename.substr(role.filename.lastIndexOf(".")+1, 3).toLowerCase() == 'pdf'){
				myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
				myLink.innerHTML = '<img src="images/pdf_icon.jpg" />';
			}
	        break;

	      case 'audio' :
	        if (role.name == 'original_content' && asset.roles.length == 1){
				myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
				$(myLink).after(role.filename);
				myLink.innerHTML = '<img src="images/spinner.gif" alt="Conversion processing"/>';
			}else if (role.name == "custom_mp3_full") {
	          myLink.setAttribute('href',location.file_url);
	          myLink.innerHTML = '<img src="images/audio_icon.jpg" />';
	          $(myLink).after(role.filename);

	          $(myLink).each(function() {
	            $(this).fancybox({
	              'type' : 'iframe',
	              'href' : '<?php echo $docroot ?>/1-advanced_demo/_audio_player.php?file=' + $(this).attr('href') + '&name=' + $(this).attr('name')
	            });
	          });
	        }
	        break;

		   case 'other' :
	        if (role.name == "original_content") {
		  		myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
				$(myLink).after(role.filename);
		        myLink.innerHTML = '<img src="images/other_icon.png" />';
	        }
	        break;
	    }
	});
	return true;
}

$(document).ready(function() {

  var api = new DropioApiClient("<?php echo $API_KEY ?>","<?php echo $docroot ?>/DropioJSClientXDReceiver.html");

  var dropCB = function(response, status){
    var chatPass = "<?php echo $chatPass ?>";

    DropioStreamer.start("<?php echo $_GET['drop_name'] ?>",chatPass,"<?php echo $docroot ?>/streamer_xdr.html");
    DropioStreamer.observe(DropioStreamer.ASSET_UPDATED, updateAsset);

  return status;
  };

  
  api.getDrop({ name : "<?php echo $_GET['drop_name']?>" }, dropCB );

  var asset;

  var assetCallback = function(assetjson) {
    asset = eval('(' + assetjson + ')');

    // Create the new element
    var newAsset = document.createElement('div');
    newAsset.setAttribute('class','thumb');

    var myLink = document.createElement('a');
    myLink.setAttribute('class','fancy'+asset.type);
    myLink.setAttribute('id',asset.name);
	
	/*
    switch(asset.type)
    {
      case 'image' :
        myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
        $(myLink).fancybox({'type':'image'});
		myLink.innerHTML = '<img src="images/spinner.gif" alt="Conversion processing"/>';
        break;
      case 'movie' :
        myLink.setAttribute('href','#');
		myLink.innerHTML = '<img src="images/spinner.gif" alt="Conversion processing"/>';
        break;
      case 'audio' :
        myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
        myLink.setAttribute('name', asset.name);
		myLink.innerHTML = '<img src="images/audio_icon.png" alt="'+asset.name+'"/>';
        break;
      case 'document' :
        myLink.setAttribute('href','#');
		myLink.innerHTML = '<img src="images/pdf_icon.png" alt="'+asset.name+'"/>';
        break;
	  case 'other' :
        myLink.setAttribute('href',asset.roles[0].locations[0].file_url);
        myLink.setAttribute('name', asset.name);
		myLink.innerHTML = '<img src="images/other_icon.png" alt="'+asset.name+'"/>';
        break;
    }
	*/
    newAsset.appendChild(myLink);

    // Get the containing div. If it does not exist then create it
    typecont = document.getElementById(asset.type+'-container');

    if (typecont == null)
    {
      // Create the container
      typecont = document.createElement('div');
      typecont.setAttribute('style','clear:both');
      typecont.setAttribute('id',asset.type+'-container');
      typecont.innerHTML = '<h2>' + asset.type + '</h2>';

      document.getElementById('content-container').appendChild(typecont);
    }
    document.getElementById(asset.type+'-container').appendChild(newAsset);
	updateAsset(asset);
    return true;
  };
  <?php
    $sigdata = '';
    if(!empty($API_SECRET)){
      $timestamp = time() + (60 * 60);
      $paramsToSign = array();
      $paramsToSign["version"] = "3.0";
      $paramsToSign["timestamp"] = $timestamp;
      $paramsToSign["signature_mode"] = "OPEN";
      $paramsToSign["api_key"] = $API_KEY;
      $signedParams = Dropio_Api::getInstance($API_KEY, $API_SECRET)->signRequest($paramsToSign);
      $sigdata .= ',"timestamp":"'.$timestamp.'","signature_mode":"OPEN","signature":"'.$signedParams["signature"].'"';
    }
  ?>
  $('#file').uploadify({
      'uploader'        : '../utils/uploadify/uploadify.swf',
      'script'          : '<?php echo Dropio_Api::UPLOAD_URL; ?>',
      'multi'           : true,
      'scriptData'      : {
        "api_key"       : "<?php echo $API_KEY ?>",
        "drop_name"     : "<?php echo $_GET['drop_name']?>",
        "format"        : "json",
        "version"       : "3.0",
        "pingback_url"  : "<?php echo $docroot ?>/1-advanced_demo/pingback.php"<?php echo $sigdata; ?>
      },
      'cancelImg'       : '../utils/uploadify/cancel.png',
      'auto'            : true,
      'onComplete'      : function(e, q, f, role, d) {
        $.post("<?php echo $docroot ?>/1-advanced_demo/ajax/upload_complete.php", {'asset':role}, function(data) {
          return true;
        });
        assetCallback(role);
        return true;
      },
      'onAllComplete'   : function() { return true; },
      'onError'         : function(e, q, f, o) { alert("ERROR: " + o.info + o.type); console.log(e);console.log(q);console.log(f);console.log(o); },
      'folder'          : '/uploads'
  });
});
// ]]></script>

<input type="file" name="fileUpload" id="file"/>
