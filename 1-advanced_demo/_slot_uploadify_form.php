<script type="text/javascript" src="../js/RMB-Javascript-API/RichMediaBackboneAPI.js"></script>
<script type="text/javascript" src="../utils/uploadify/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../utils/uploadify/swfobject.js"></script>
<script type="text/javascript" src="../utils/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="../utils/uploadify/uploadify.css" />

<script type="text/javascript">// <![CDATA[
// This is the callback called on the watcher
function theCallback(){
  alert('called');
}
$(document).ready(function() {

var assetCallback = (function(e) {
   alert(e);

});

$('#file').uploadify({
'uploader'  : '../utils/uploadify/uploadify.swf',
'script'    : 'http://assets.drop.io/upload',
'multi'     : true,
'scriptData': {"api_key":"ef39e3464ace103e529016d0e7379da66ff50731","drop_name":"sheraznew","format":"json","version":"3.0","pingback_url":"http://dropio.m3b.net/1-advanced_demo/pingback.php"},
'cancelImg' : '../utils/uploadify/cancel.png',
'auto'      : true,
'onComplete' : function(e,q,f,r,d){
    
    alert(r + f.type);
    
    return true;
},
'onAllComplete' : function(){return true;},
'onError'   : function(e, q, f, o) { alert("ERROR: " + o.info + o.type); }, 
'folder'    : '/uploads'
});



});
// ]]></script>

<input type="file" name="fileUpload" id="file"/>
