<?php
function get_file_url($a,$type)
{
    $vals = json_decode($a['values'],true);

    if ($vals == NULL || $vals==FALSE)
        return 'images/spinner.gif'; 

    # Dig down into that array
    foreach($vals['roles'] as $v)
    {
        if ($v['name'] == $type)
            return ($a['is_complete']==0) ? 'images/spinner.gif' : $v['locations'][0]['file_url'];
    }
}

function show_image($a)
{
    return '<a class="fancyimg" href="'.get_file_url($a,'original_content').'"><img src="'. get_file_url($a,'thumbnail') . '" alt=""/></a>' . substr($a['name'],0,15) . '<br/>';
}

function show_audio($a)
{
    $file_url = get_file_url($a,'original_content');
    $k = uniqid();
    $html = <<<EOF
    <p id="audioplayer_$k">Alt content</p>
         <script type="text/javascript">
          AudioPlayer.embed("audioplayer_$k", {soundFile: "$file_url"});
         </script>
         <b>Name:</b> {$a['name']}<br/>
         <b>Description:</b>
    
EOF;
    return $html;
}

function show_document($a)
{
    return '<a href="'. get_file_url($a,'original_content').'"><img src="images/pdf_icon.jpg"/></a>' . substr($a['name'],0,15) . '<br/>';
}

function show_movie($a)
{
    return '<a class="fancy" href="'.get_file_url($a,'original_content').'"><img src="'. get_file_url($a,'thumbnail') . '" alt=""/></a>' . substr($a['name'],0,15) . '<br/>';
}