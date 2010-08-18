<?php

Interface RoleType
{

    public function getPreview($type);

}

Abstract Class Role Implements RoleType
{
    
    const DEF_LOCATION  = 'DropioS3';
    private $values    = array();

    public function __construct()
    {

    }

    public function getName()     { return $this->values['name']; }
    public function getFileSize() { return $this->values['filesize']; }
    public function getName()     { return $this->values['name']; }

    public function getLocation($name=self::DEF_LOCATION)
    {
      foreach($this->values['locations'] as $loc)
      {
        if($loc['location']['name'] == $name)
            return $loc['location'];
      }

      return false;
    }

    public function getStatus($location=self::DEF_LOCATION)
    {
        $loc = $this->getLocation($location);
        return (isset($loc['location']['status']) ? $log['location']['status'] : false;
    }

    public function getFileUrl($location=self::DEF_LOCATION)
    {
        $loc = $this->getLocation($location);
        return (isset($loc['location']['file_url']) ? $log['location']['file_url'] : false;
    }
}
