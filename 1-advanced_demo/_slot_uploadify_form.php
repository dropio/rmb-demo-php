<script type="text/javascript" src="../utils/uploadify/swfobject.js"></script>
<script type="text/javascript" src="../utils/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="../utils/uploadify/uploadify.css" />

<script type="text/javascript">// <![CDATA[

$(document).ready(function() {

    var api = new DropioApiClient("<?php echo $API_KEY ?>","<?php echo $docroot ?>/DropioJSClientXDReceiver.html");

    var chatPass = 'broken';

    var dropCB = function(response, status){
        chatPass = response.chat_password;
        console.log('get drop called with ' + chatPass);
        DropioStreamer.start("<?php echo $_GET['drop_name'] ?>",chatPass,"<?php echo $docroot ?>/streamer_xdr.html");
        DropioStreamer.observe(DropioStreamer.ASSET_UPDATED,function(data){
            console.log('asset updated called');

            // Shortcut to the objects we want
            var r = data.roles.role;
            var l = r.locations.location;

            // Bail out if the status is anything but complete
            if (l.status !== 'complete') { return true; }

            // Deal with the role based on it's type
            switch(r.name)
            {
                case 'thumbnail' :
                    document.getElementById(data.name).innerHTML = '<a href="#"><img src="' + l.file_url + '"/></a>';
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

        // Create the new element
        var newAsset = document.createElement('div');
        newAsset.setAttribute('class','thumb');
        newAsset.setAttribute('id',j.name);
        newAsset.innerHTML = '<img src="images/spinner.gif"/>';
        
        // Get the containing div. If it does not exist then create it
        typecont = document.getElementById(j.type+'-container');
        console.log(typecont);
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

$('#file').uploadify({
    'uploader'  : '../utils/uploadify/uploadify.swf',
    'script'    : 'http://assets.drop.io/upload',
    'multi'     : true,
    'scriptData': {"api_key":"<?php echo $API_KEY ?>","drop_name":"<?php echo $_GET['drop_name']?>","format":"json","version":"3.0","pingback_url":"<?php echo $docroot ?>/1-advanced_demo/pingback.php"},
    'cancelImg' : '../utils/uploadify/cancel.png',
    'auto'      : true,
    'onComplete' : function(e,q,f,r,d) {
        console.log("calling complete with:");
        console.log(r);
        assetCallback(r);
        return true;
    },
    'onAllComplete' : function(){return true;},
    'onError'       : function(e, q, f, o) { alert("ERROR: " + o.info + o.type); },
    'folder'        : '/uploads'
    });
});
// ]]></script>

<input type="file" name="fileUpload" id="file"/>
