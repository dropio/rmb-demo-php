<?php

include_once 'config.php';

include_once 'PHPUnit/Framework.php';
include_once '../Dropio/Asset.php';


class Dropio_AssetTest extends PHPUnit_Framework_TestCase
{
    const PAGINATION = 31; // Test pagination

    private $asset = null;

    public function setup()
    {
      $this->asset = new Dropio_Asset(Fixture::API_KEY);
    }

    public function tearDown()
    {

    }

    public function testUploadAFile()
    {
        $this->assertTrue(False);
    }
    
    public function testGetInfoOnAsset()
    {
        $this->assertTrue(False);
    }

    public function testCreateALink()
    {
        $this->assertTrue(False);
    }
    
    public function testCreateANote()
    {
        $this->assertTrue(False);
    }
    
    public function testDownloadOriginalFile()
    {
        $this->assertTrue(False);
    }
    
    public function testUpdateAnAsset()
    {
        $this->assertTrue(False);
    }

    public function testDeleteAnAsset()
    {
        $this->assertTrue(False);
    }
    
    public function testMoveAnAsset()
    {
        $this->assertTrue(False);
    }
    
    public function testCopyAnAsset()
    {
        $this->assertTrue(False);
    }
}
