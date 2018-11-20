<?php

class MOVIE_NETFLIX extends MOVIE
{
    protected $NetflixId;   // INT
    protected $NetflixURL;  // STRING

    public function LoadFromObject($object)
    {
        $this->Title = $object["title"];
        $this->RunTime = ($object["runtime"] / 60);
        $this->Image = urldecode($object["image"]);

        $this->NetflixId = $object["netflixId"];
        $this->NetflixURL = urldecode($object["netflixURL"]);
		$this->Url = urldecode($object["netflixURL"]);
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