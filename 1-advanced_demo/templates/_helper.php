<?php
function get_file_url($a,$role)
{
    $vals = json_decode($a['values'],true);

    if ($vals == NULL || $vals==FALSE)
        return 'images/spinner.gif';

    # Dig down into that array
    foreach($vals['roles'] as $v)
    {
        if ($v['name'] == $role)
            return ($a['is_complete']==0) ? 'images/spinner.gif' : $v['locations'][0]['file_url'];
    }
}

function get_name($a)
{
  $vals = json_decode($a['values'], true);
  return $vals['name'];
}

function show_image($a)
{
    return '<a class="fancyimage" href="'.get_file_url($a,'custom_large_thumb').'"><img src="'. get_file_url($a,'custom_small_thumb') . '" alt=""/></a><div id="shortname">' . get_name($a) . '</div><br/>';
}

function show_audio($a)
{
    $file_url = get_file_url($a,'custom_mp3_full');
    $name = get_name($a);
    return '<a class="fancyaudio" href="' . $file_url . '"  name="' . $name .  '"><img src="images/audio_icon.jpg" /></a>' . '<div id="shortname">' . $name . '</div><br />';
}

/**
 *  Get an HTML snippet that shows an icon and links to a document
 */
function show_document($a)
{
    if (substr($a['name'], -3, 3) == "pdf") {
        # Document is a PDF. Link to original content.
        $doclink = 'original_content';
    } else {
        # Document is not a PDF. Link to web preview.
        $doclink = 'web_preview';
    }
    return '<a class="fancydocument" href="' . get_file_url($a, $doclink) . '"><img src="images/pdf_icon.jpg"/></a><div id="shortname">' . $a['name'] . '</div><br/>';
}

function show_movie($a)
{
    return '<a class="fancymovie" href="'.get_file_url($a,'web_preview').'" poster="'.get_file_url($a,'large_thumbnail').'" ><img src="'. get_file_url($a,'thumbnail') . '" alt=""/></a><div id="shortname">' . $a['name'] . '</div><br/>';
}