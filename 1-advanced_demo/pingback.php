<?php
/**
 * This is the core file for pingbacks. It will determine the appropriate
 * action to take based on the ping coming back from drop.io.
 *
 */

# Bootstrap the page for app-wide functions. This means loading configs, classes,
# and sesison variables
$handled_pingback_functions = array('ASSET_UPDATED');


include_once '_bootstrap.php';

# Load the user functions
include_once('user_funcs.php');

$event = strtolower($_POST['event']);

# Are we debugging this?
//if (DEBUG===true)
//{
	dolog(implode(' ',array_keys($_REQUEST))) ;
    dolog($_POST['event']);
    dolog($_POST['asset']);
//}

# Call the appropriate function for the kind of event pinging back to us
if(in_array($event, $handled_pingback_functions)){
	call_user_func($event, $_POST['asset']);
}else{
	//if (DEBUG === true){
		dolog('Unhandled pingback call: ' . $event);
	//}
}