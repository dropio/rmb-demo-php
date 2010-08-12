<?php
function get_thumb($a,$type)
{
    $vals = json_decode($a['values'],true);

    # Dig down into that array
    foreach($vals['roles'] as $v)
    {
                    
        if ($v['name'] == $type)
        {
            if ($v['locations'][0]['status'] === 'pending')
                return '/images/spinner.gif';
            else
                return $v['locations'][0]['file_url'];
        }
            
    }
}
