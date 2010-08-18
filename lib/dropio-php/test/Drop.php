<?php

include_once 'config.php';

include_once 'PHPUnit/Framework.php';
include_once '../Dropio/Drop.php';


class Dropio_Drop_Test extends PHPUnit_Framework_TestCase
{
    const PAGINATION = 31; // Test pagination

    protected $api   = null;
    protected $drop  = null;
    protected $drops = array(); //array of drops

    public function setup()
    {
      $this->drop = new Dropio_Drop(Fixture::API_KEY);
    }

    public function tearDown()
    {

    }

    public function testListAssets()
    {

    }

    public function testAddAsset()
    {

    }

    public function deleteAsset()
    {

    }

    public function testAddDrop()
    {

    }

    public function testDeleteDrop()
    {

    }

    public function testUpdateDrop()
    {

    }




}
