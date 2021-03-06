<?php

class FEED_EVENT extends RTT_COMMON
{
    const PROFILE = "PROFILE";

    public $EventID;
    public $ReclineeID;
    public $Text;
    public $Timestamp;
    public $Image;

    protected $Reclinee;

    function __construct()
    {

    }

    function LoadFromRow($row)
    {
        $this->EventID = $row["EventID"];
        $this->ReclineeID = $row["ReclineeID"];
        $this->Text = $row["Text"];
        $this->Timestamp = $row["Timestamp"];
        $this->Image = $row["Image"];

        return $this;
    }

    function LoadFromArgs($eventID, $reclineeID, $text, $timestamp, $image)
    {
        $this->EventID = $eventID;
        $this->ReclineeID = $reclineeID;
        $this->Text = $text;
        $this->Timestamp = $timestamp;
        $this->Image = $image;

        return $this;
    }

    function GetImageURL()
    {
        if($this->Image == self::PROFILE)
            return $this->GetProfilePicture();
        else
            return $this->Image;
    }

    function GetDisplayName()
    {
        if($this->ReclineeID == 0) {
            return "Reclinathon";
        }

        if(!$this->Reclinee) {
            $this->Reclinee = new RECLINEE();
            $this->Reclinee->Load($this->ReclineeID);
        }
        return $this->Reclinee->GetDisplayName();
    }

    function FormatAJAXResponse()
    {
        $result = "<FeedEvent>";
        $result .= "<EventID>" . $this->EventID . "</EventID>";
        $result .= "<ReclineeID>" . $this->ReclineeID . "</ReclineeID>";
        $result .= "<DisplayName>" . $this->GetDisplayName() . "</DisplayName>";
        $result .= "<Message>" . htmlentities($this->Text) . "</Message>";
        $result .= "<Timestamp>" . $this->Timestamp . "</Timestamp>";
        $result .= "<Icon>" . $this->GetImageURL() . "</Icon>";
        $result .= "</FeedEvent>";

        return $result;
    }

    private function GetProfilePicture()
    {
        return "images/" . $this->ReclineeID . ".png";
    }
}

?>