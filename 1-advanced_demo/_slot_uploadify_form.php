<script type="text/javascript" src="../utils/uploadify/swfobject.js"></script>
<script type="text/javascript" src="../utils/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="../utils/uploadify/uploadify.css" />

<?php
  $drop_name = $_GET['drop_name'];
  $drop = Dropio_Drop::getInstance($API_KEY, $API_SECRET)->load($drop_name);
  $chatPass = $drop->getChatPassword();
?>

<script type="text/javascript">// <![CDATA[

$(document).ready(function() {

    var api = new DropioApiClient("<?php echo $API_KEY ?>","<?php echo $docroot ?>/DropioJSClientXDReceiver.html");

    var dropCB = function(response, status){
        var chatPass = "<?php echo $chatPass ?>";

        DropioStreamer.start("<?php echo $_GET['drop_name'] ?>",chatPass,"<?php echo $docroot ?>/streamer_xdr.html");
        DropioStreamer.observe(DropioStreamer.ASSET_UPDATED,function(data){

            // Shortcut to the objects we want
            var type = data.type;         // image, movie, document, etc
            var r = data.roles.role;
            var l = r.locations.location;

            console.log('checking role');
            console.log(r);

            // Bail out if the status is anything but complete
            if (l.status !== 'complete') { return; }

            // Deal with the role based on it's type
            switch (type)
            {
              case 'image' :
                console.log('inside switch');
                if (r.name == 'thumbnail') {
                  console.log('r.name is thumbnail: ' + l.file_url);
                  document.getElementById(data.name).innerHTML = '<img src="' + l.file_url + '"/>';
                }
                break;

              case 'movie'  :
                if (r.name == 'thumbnail') {
                  document.getElementById(data.name).innerHTML = '<img src="' + l.file_url + '"/>';
                }
                if (r.name == 'web_preview') {
                  var myLink = document.getElementById(data.name);
                  myLink.setAttribute('href',l.file_url);
                  $(myLink).each(function(){
                    $(this).fancybox({
                      'type' : 'iframe',
                      'href' : '<?php echo $docroot ?>/1-advanced_demo/_video_player.php?file=' + $(this).attr('href') + '&poster=' + $(this).attr('poster')
                    });
                  });

                }
                if (r.name == 'large_thumbnail') {
                  document.getElementById(data.name).setAttribute('poster',l.file_url);
                }

                break;

              case 'document' :
                if (r.name == 'web_preview') {
                  var myLink = document.getElementById(data.name);
                  myLink.innerHTML = '<img src="images/pdf_icon.jpg"/>';
                  $(myLink).each(function(){
                    $(this).fancybox({
                      'type' : 'iframe',
                      'href' : 'http://docs.google.com/viewer?embedded=true&url=' + $(this).attr('href')
                    });
                  });

                }
                break;

              case 'audio' :

                break;
            }

        });

        return status;
    };

    api.getDrop({ name : "<?php echo $_GET['drop_name']?>" }, dropCB );

    var j;

    var assetCallback = function(e) {
       //console.log("asset callback call");
        j = eval('(' + e + ')');

        console.log(j);

        //console.log('Original Content: ' + j.roles[0].locations[0].file_url);
        // Create the new element
        var newAsset = document.createElement('div');
        newAsset.setAttribute('class','thumb');

        var myLink = document.createElement('a');
        myLink.setAttribute('class','fancy'+j.type);
        myLink.setAttribute('id',j.name);

        switch(j.type)
        {
          case 'image' :
            myLink.setAttribute('href',j.roles[0].locations[0].file_url);
            $(myLink).fancybox({'type':'image'});
            break;
          case 'movie' :
            myLink.setAttribute('href','#');
            break;
          case 'audio' :
            myLink.setAttribute('href',j.roles[0].locations[0].file_url);
            break;
          case 'document' :
            myLink.setAttribute('href','#');
            break;
        }

        myLink.innerHTML = '<img src="images/spinner.gif" alt="Not ready yet"/>';
        newAsset.appendChild(myLink);

        // Get the containing div. If it does not exist then create it
        typecont = document.getElementById(j.type+'-container');

        if (typecont == null)
        {
          // Create the container
          typecont = document.createElement('div');
          typecont.setAttribute('style','clear:both');
          typecont.setAttribute('id',j.type+'-container');
          typecont.innerHTML = '<h3>' + j.type + '</h3>';

          document.getElementById('content-container').appendChild(typecont);
        }
        document.getElementById(j.type+'-container').appendChild(newAsset);
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
      'script'          : 'http://assets.drop.io/upload',
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
      'onComplete'      : function(e,q,f,r,d) {
          assetCallback(r);
          return true;
      },
      'onAllComplete'   : function() { return true; },
      'onError'         : function(e, q, f, o) { alert("ERROR: " + o.info + o.type); },
      'folder'          : '/uploads'
  });
});
// ]]></script>

<input type="file" name="fileUpload" id="file"/>
