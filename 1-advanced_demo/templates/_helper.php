<?php
function get_thumb($a,$type)
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
