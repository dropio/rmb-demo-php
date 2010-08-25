<?php

include_once 'config.php';

include_once 'PHPUnit/Framework.php';
include_once '../Dropio/Api.php';


class Dropio_ApiTest extends PHPUnit_Framework_TestCase
{
    const PAGINATION = 31; // Test pagination

    protected $api   = null;
    protected $drop  = null;
    protected $drops = array(); //array of drops

    public function setup()
    {
      $this->api = new Dropio_Api(Fixture::API_KEY);
    }

    public function tearDown()
    {

    }

    /**
     * @expectedException Dropio_Api_Exception
    */
    public function testNoApiKeyInConstructorException()
    {
        $tmp = new Dropio_Api();

    }
    
    public function testGetAListOfDrops()
    {
        $drops = $this->api->getDrops();
        $this->assertTrue(is_array($drops));
        $this->assertTrue(isset($drops['total']));
        $this->assertTrue($drops['total'] > 0);
        //var_dump($drops);
    }

}
