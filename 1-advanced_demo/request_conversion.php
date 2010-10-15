<?php
/**
 * This is the local proxy for starting conversion for an asset. 
 * It receives requests to convert an asset from javascript,
 * decides what targets that file should be converted to,
 * and sends them on to the RMB API
 *
 */


function request_conversion($asset_id, $asset_type){

	# Bootstrap the page for app-wide functions. This means loading configs, classes,
	# and sesison variables
	include_once '_bootstrap.php';

	# Load the api helper library
	include_once(dirname(__FILE__) . '/../lib/rmb-php/Rmb/Api.php');

	$pingback_url = $docroot . "/1-advanced_demo/pingback.php";
	//$asset_id = intval($_REQUEST['asset_id']);
	//$asset_type = strtoupper($_REQUEST['asset_type']);

	/////////////////////////////////
	//Define the conversions we want. These are arbitrary and you can request as many
	//conversions for a file as you like, providing the conversion job can be performed
	//on the asset_type specified
	////////////
	//These definitions are used below when we actually request the conversion for the asset
	////////////
	//IMAGE conversion options
	$custom_small_thumb = array(
							"role" => "custom_small_thumb", 
							"params" => array(
								"width" => 150,
								"height" => 150,
								"resize_type" => "FILL"
							)
						  );
	$custom_large_thumb = array(
							"role" => "custom_large_thumb", 
							"params" => array(
								"width" => 1000,
								"height" => 600,
								"resize_type" => "FIT"
							)
						  );
	///////////
	//AUDIO
	$custom_mp3_full = array(
							"role" => "custom_mp3_full", 
							"params" => array(
								"content_type" => "audio/mpeg"
							)
						  );
	$custom_mp3_30sec = array(
							"role" => "custom_mp3_30sec", 
							"params" => array(
								"content_type" => "audio/mpeg",
								"start" => 15,
								"duration" => 30
							)
						  );
	///////////
	//DOCUMENTS
	$custom_pdf = array(
							"role" => "custom_pdf", 
							"params" => array(
								"content_type" => "application/pdf"
							)
						  );
	$custom_flv = array(
							"role" => "custom_flv", 
							"params" => array(
								"content_type" => "application/x-shockwave-flash"
							)
						  );
	//////////
	//MOVIES
	$custom_mp4 = array(
							"role" => "custom_mp4", 
							"params" => array(
								"format" => "mp4",
								"width" => "640",
								"height" => "480"
							)
						  );
	$custom_movie_mp3 = array(
							"role" => "custom_mp3", 
							"params" => array(
								"format" => "mp3"
							)
						  );
	$custom_movie_thumb = array(
							"role" => "custom_movie_thumb", 
							"params" => array(
								"format" => "jpg",
								"width"  => 320,
								"height" => 240
							)
						  );
	$custom_movie_poster = array(
							"role" => "custom_movie_poster", 
							"params" => array(
								"format" => "jpg",
								"width"  => 640,
								"height" => 480
							)
						  );

	//set up a shared base for all outputs, since we always want our conversions to trigger pingbacks
	//and attach themselves to the same asset they were sourced from
	$output_base = array("asset_id" => $asset_id);

	//The input will always be the original_content of the asset we are passing in:
	$inputs = array(array("asset_id" => $asset_id, "name" => "source", "role" => "original_content"));

	if(!empty($_POST['locations'])){ $output_base["locations"] = $_POST['locations']; }
	$outputs = array();

	//Ensure that asset_type is uppercase
	$asset_type = strtoupper($asset_type);
	try{
		if($asset_type == "IMAGE"){
			$outputs[] = array_merge($output_base, $custom_small_thumb);
			$outputs[] = array_merge($output_base, $custom_large_thumb);
			$using = "DropioImageConverter";
			Rmb_Api::getInstance($API_KEY, $API_SECRET)->convert($asset_type, $inputs, $outputs, $using, $pingback_url);
		}else if($asset_type == "AUDIO"){
			$outputs[] = array_merge($output_base, $custom_mp3_full);
			$outputs[] = array_merge($output_base, $custom_mp3_30sec);
			$using = "DropioAudioConverter";
			Rmb_Api::getInstance($API_KEY, $API_SECRET)->convert($asset_type, $inputs, $outputs, $using, $pingback_url);
		}else if($asset_type == "DOCUMENT"){
			$outputs[] = array_merge($output_base, $custom_pdf);
			$outputs[] = array_merge($output_base, $custom_flv);
			$using = "ScribdConverter";
			Rmb_Api::getInstance($API_KEY, $API_SECRET)->convert($asset_type, $inputs, $outputs, $using, $pingback_url);
		}else if($asset_type == "MOVIE"){
			$outputs[] = array_merge($output_base, $custom_mp4);
			//$outputs[] = array_merge($output_base, $custom_movie_mp3);
			$outputs[] = array_merge($output_base, $custom_movie_thumb);
			$outputs[] = array_merge($output_base, $custom_movie_poster);
			//$using = "EncodingDotComConverter";
			$using = "DropioMovieConverter";
			Rmb_Api::getInstance($API_KEY, $API_SECRET)->convert($asset_type, $inputs, $outputs, $using, $pingback_url);
		}else{
			//don't request conversion for other files (zips, etc)
		}
	}catch(Rmb_Api_Exception $e){
		return(json_encode(array("response"=>array("result" => "Error", "message" => $e->getMessage()))));
	}
	return true;
}