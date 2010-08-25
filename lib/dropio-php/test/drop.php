<?php

include_once 'config.php';

include_once 'PHPUnit/Framework.php';
include_once '../Dropio/Drop.php';


class Dropio_DropTest extends PHPUnit_Framework_TestCase
{
    const PAGINATION = 31; // Test pagination

    protected $drop  = null;

    public function setup()
    {
      $this->drop = new Dropio_Drop(Fixture::API_KEY);
    }

    public function tearDown()
    {

    }

    public function testListAssets()
    {
      $this->drop->setName('yourmomgoestocollege')->load();
    }

    public function testListAssetsWithPagination()
    {
      $this->assertTrue(FALSE);
    }

    public function testAddAsset()
    {

      $this->assertTrue(FALSE);
    }

    public function deleteAsset()
    {
      $this->assertTrue(FALSE);

    }

    public function testCreateADrop()
    {
      $this->assertTrue(FALSE);

    }

    public function testDeleteADrop()
    {
      $this->assertTrue(FALSE);

    }

    public function testUpdateADrop()
    {
      $this->assertTrue(FALSE);

    }

    public function testEmptyADrop()
    {
      $this->assertTrue(FALSE);

    }


}
