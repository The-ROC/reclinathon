<?php

class MOVIE_NETFLIX extends MOVIE
{
    protected $NetflixId;   // INT
    protected $NetflixURL;  // STRING

    public function LoadFromObject($object)
    {
        $this->Title = $object["title"];
        $this->RunTime = $object["runtime"];
        $this->Image = $object["image"];

        $this->NetflixId = $object["netflixId"];
        $this->NetflixURL = $object["netflixURL"];
    }

    public function GetNetflixId()
    {
        return $this->NetflixId;
    }

    public function GetNetflixURL()
    {
        return $this->NetflixURL;
    }
}

?>