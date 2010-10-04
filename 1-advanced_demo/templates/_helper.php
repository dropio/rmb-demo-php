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

function get_original_filename($a)
{
  $vals = json_decode($a['values'],true);

  if (!($vals == NULL || $vals == FALSE))
  {
    foreach($vals['roles'] as $v)
    {
      if ($v['name'] == "original_content")
      {
        return $v['locations'][0]['filename'];
      }
    }
  }
}

function get_name($a)
{
  $vals = json_decode($a['values'], true);
  return $vals['name'];
}

function show_image($a)
{
    return '<a class="fancyimage" href="'.get_file_url($a,'custom_large_thumb').'"><img src="'. get_file_url($a,'custom_small_thumb') . '" alt=""/></a>' . get_name($a) . '<br/>';
}

function show_audio($a)
{
    $file_url = get_file_url($a,'custom_mp3_full');
    $name = get_name($a);
    return '<a class="fancyaudio" href="' . $file_url . '"  name="' . $name .  '"><img src="images/audio_icon.jpg" /></a>' . $name . '<br />';
}

/**
 *  Get an HTML snippet that shows an icon and links to a document
 */
function show_document($a)
{
    if (substr(get_original_filename($a), -3, 3) == "pdf") {
        # Document is a PDF. Link to original content.
        $doclink = get_file_url($a,'original_content');
    } else {
        # Document is not a PDF. Link to web preview.
        $doclink = get_file_url($a,'custom_pdf');
    }
  $name = get_name($a);
    return '<a class="fancydocument" href="' . $doclink . '"><img src="images/pdf_icon.jpg"/></a>' . $name . '<br/>';
}

function show_movie($a)
{
	$name = get_name($a);
    return '<a class="fancymovie" href="'.get_file_url($a,'custom_mp4').'" poster="'.get_file_url($a,'custom_movie_poster').'" ><img src="'. get_file_url($a,'custom_movie_thumb') . '" alt=""/><div class="bttn-play"></div></a>' . $name . '<br/>';
}

function show_other($a)
{
  $file_url = get_file_url($a, 'original_content');
  return '<a class="fancyother" href="'.get_file_url($a,'original_content').'"><img src="images/other_icon.jpg" /></a></a>' . substr($a['name'],0,15) . '<br/>';
}