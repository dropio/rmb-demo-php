<?php

include_once 'Data.php';
include_once 'Set.php';
include_once 'Drop.php';
include_once 'Drop/Set.php';
include_once 'Asset.php';
include_once 'Drop/Subscription.php';
include_once 'Drop/Subscription/Set.php';
include_once 'Asset/Set.php';
include_once 'Asset/Comment.php';
include_once 'Asset/Comment/Set.php';
 
if (!extension_loaded('curl')) {
  throw new Dropio_Exception('This library requires the Curl extension.  Read more: http://php.net/manual/en/book.curl.php');
} 

Class Dropio_Exception extends Exception {};
Class Dropio_Api_Exception extends Dropio_Exception {};

/**
 * Dropio_Api is a client for the Dropio API and the basis for all the other 
 * helper classes.
 * 
 * This can be used to access all the functionality of the API.  
 * 
 * If an error is returned from the API, a Dropio_Api_Exception is thrown.
 * 
 * Example to get details on a drop.
 * 
 * try {
 *  $api = new Dropio_Api(API_KEY);
 *  $response = $api->request('GET', '/drops/php_api_lib');
 *  print_r($response);
 * } catch (Dropio_Api_Exception $e) {
 *  die("Error:" . $e->getMessage());
 * }
 *
 */

Class Dropio_Api {

  const RESPONSE_FORMAT  = 'json';
  const API_VERSION      = '3.0';

  protected $api_key     = null;
  protected $api_secret  = null;

  static $global_api_key    = null;
  static $global_api_secret = null;

  static $use_https      = false;
  static $api_url        = null;

  const API_HTTP_URL     = 'http://api.drop.io';
  const API_HTTPS_URL    = 'http://api.drop.io';

  const CLIENT_VER       = '1.0';
  const UPLOAD_URL       = 'http://assets.drop.io/upload';

  /**
	 * instantiates a new Dropio_Api object.  The api_key is optional, if not set
	 * it uses the global api_key set by: Dropio_Api::setKey(API_KEY);
	 *
	 * @param string $api_key
	 */

  public function __construct ( $api_key = null, $api_secret = null ) {

    if (empty($api_key)) {
      $api_key = self::$global_api_key;
    }

    if (empty($api_secret)) {
      $api_secret = self::$global_api_secret;
    }

    if (empty($api_key)) {
      throw new Dropio_Api_Exception('Api key is not set.');
    }

    $this->api_key = $api_key;
    $this->api_secret = $api_secret;
  }

  /**
	 * Instance method to allow simple chaining.
	 * 
	 * Example:
	 * 
	 * $response = Dropio_Api::instance()->request('GET', '/drops/php_api_lib');
	 *
	 * @param string $api_key
   * @param string $api_secret
	 * @return Dropio_Api
	 */

  public static function instance ( $api_key=null, $api_secret=null)
  {
    if (empty($api_key))
      $api_key = self::$global_api_key;

    if (empty($api_secret))
      $api_secret = self::$global_api_secret;

    return new Dropio_Api( $api_key, $api_secret );
  }

  /**
	 * Sets the global api_key.
	 *
	 * @param string $api_key
   * @param string $api_secret the api secret (optional)
	 */

  public static function setKey( $api_key = null, $api_secret = null ) {
    self::$global_api_key = $api_key;
    self::$global_api_secret = $api_secret;
  }

  /**
  * Sets the global api_url.
  *
  * @param string $url
  */

  public static function setApiUrl( $url ) {
      self::$api_url = $url;
  }
	
  /**
    * Executes a request to Drop.io's API servers.
    *
    * @param string $method
    * @param string $path
    * @param array $params
    * @return mixed
    */

  public function request ( $method, $path, $params = Array() ) {

    $params['version'] = self::API_VERSION;
    $params['format']  = self::RESPONSE_FORMAT;

    $params['api_key'] = $this->api_key;

    $api_url  = empty(self::$api_url) ? (self::$use_https ? self::API_HTTPS_URL : self::API_HTTP_URL) : self::$api_url;

    $url      =  $api_url . '/' . $path;

    // Sign it, damn you!!
    $params = $this->sign_if_needed($params);

    $ch = curl_init();

    /**
    *  Setting the user agent, useful for debugging and allowing us to check which version
    **/
		
    curl_setopt($ch, CURLOPT_USERAGENT, 'Drop.io PHP client v' . self::CLIENT_VER);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    
    switch($method){
      case 'POST':

        curl_setopt($ch, CURLOPT_POST, 1);

        //For some reason, this needs to be a string instead of an array.
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        break;
      case 'DELETE':
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        break;
      case 'PUT':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        break;
      case 'GET':
        $url .= '?' . http_build_query($params);
        break;
      case 'UPLOAD':
        $params['file'] = '@' . $params['file'];

        $url = self::UPLOAD_URL;

        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $params);
        break;
    }

//    echo $url;print_r($params); echo "\n";
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if ( ( $result = curl_exec($ch) ) === false ) {
      throw new Dropio_Api_Exception ('Curl Error:' . curl_error($ch));
    }

    $http_response_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (
    in_array($http_response_code, Array(200,400,403,404))
    &&
    is_array( $data = @json_decode( $result, true))
    ) {

      if (
      isset($data['response']['result'])
      &&
      $data['response']['result'] == 'Failure'
      ) {
        throw new Dropio_Api_Exception ($data['response']['message']);
      }

      return $data;
    }

    throw new Dropio_Api_Exception(
    'Received error code from web server:' . $http_response_code,
    $http_response_code
    );

  }

  /**
   *
   *
   *
   */
  public function sign_if_needed($params = null)
  {
    if($this->api_secret !== NULL)
    {
        $params = $this->add_required_params($params);
        $params = $this->sign_request($params);
    }

    return $params;
  }

  public function sign_request($params = null)
  {
        $str='';
        ksort($params);

        # Weird, if token is present but empty, remove it. Move this logic to
        # Drop object
        if(empty($params['token']))
          unset($params['token']);

        foreach($params as $k=>$v)
            $str .= "$k=$v";

        $params['signature'] = sha1($str . $this->api_secret);

        return $params;
  }

  public function add_required_params($params = null)
  {
      $params['timestamp'] = strtotime('now + 15 minutes');
      return $params;
  }

  /**
   * Use this static method to get a signature to make uploads
   * 
   * @param   array   $params
   * @return  array   Hash array containing signed URL and timestamp
   *                  $ret['timestamp'] = 1280787859;
   *                  $ret['signature'] = '292809f6e6d8b4bc9cbefc8ae5a287b93ed6d04c';
   *
   */
  public static function getSignature($params = null)
  {
    $params['timestamp'] = strtotime('now + 15 minutes');

    $arr['timestamp'] = $params['timestamp'];
    $arr['signature'] = Dropio_Api::instance()->sign_if_needed($params);

    return $arr;
  }

  /**
  * Simplify the process of making an upload form. Have the object return an
  * HTML snippit ready to drop into any page
  *
  * TODO: Generate the code
  */
  public static function getSimpleUploadForm()
  {
    return false;
  }

  /**
  * Get the pretty flash / javascript uploader for uploadify form uploader
  *
  * TODO: Generate the code
  */
  public static function getUploadifyForm()
  {
    return false;
  }

  /**
   * Return a list of all the drops for a given key
   * @param <type> $page
   * @return <type>
   */
  function getDrops ( $page = 1) {

    $result = $this->request('GET', 'accounts/drops',
      Array('page'=>$page)
    );

    return $result;

  }

  /**
  * Retrieves status on manager account.
  *
  * @return Array
  */
  function getStats () {

  $result = $this->request('GET', 'accounts/stats',
    Array()
  );

  return $result;

  }
}