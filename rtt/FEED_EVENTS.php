<?php

class FEED_EVENTS extends RTT_COMMON
{
    protected $FeedEvents;      // Array of FEED_EVENT

    function __construct()
    {
        
    }

    function LoadEventsAfterID($startID)
    {
        $query = "SELECT * FROM FEED_EVENTS WHERE EventID > '$startID'";
        $result = $this->Query($query);
        $this->FeedEvents = array();
        if($result)
        {
            while($row = mysql_fetch_assoc($result))
            {
				$feedEvent = new FEED_EVENT();
                $feedEvent->LoadFromRow($row);
                array_push($this->FeedEvents, $feedEvent);
            }
        }

        return $this->FeedEvents;
    }

    function PostUserMessage($reclineeID, $text, $timestamp)
    {
        return $this->PostEvent($reclineeID, $text, $timestamp, FEED_EVENT::PROFILE);
    }

    function PostSystemEvent($text, $timestamp, $image)
    {
        return $this->PostEvent(0, $text, $timestamp, $image);
    }

    function PostEvent($reclineeID, $text, $timestamp, $image)
    {
        $_text = $this->GetDatabase()->GetEscapeString($text);
        $query = "INSERT INTO FEED_EVENTS (ReclineeID, Text, Timestamp, Image) VALUES ('$reclineeID', '$_text', '$timestamp', '$image')";
        $result = $this->Query($query);
        if(!$result)
            return FALSE;
		
		$feedEvent = new FEED_EVENT();
		$feedEvent->LoadFromArgs(mysql_insert_id(), $reclineeID, $text, $timestamp, $image);
        return $feedEvent;
    }

    function FormatAJAXResponse()
    {
        $result = "<FeedEvents>";
        foreach(($this->FeedEvents) as $feedEvent)
        {
            $result .= $feedEvent->FormatAJAXResponse();
        }
        $result .= "</FeedEvents>";

        return $result;
    }
}

?>