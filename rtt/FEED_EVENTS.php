<?php

class FEED_EVENTS extends RTT_COMMON
{
    protected $FeedEvents;      // Array of FEED_EVENT

    function __construct()
    {
        
    }

    function LoadEventsAfterID($startID)
    {
        $query = $this->GetConnection()->prepare(
            "SELECT * FROM FEED_EVENTS WHERE EventID > ?"
        );
        $query->bind_param('i', $startID);
        $result = $this->Query($query);
        $this->FeedEvents = array();
        if($result)
        {
            while($row = $result->fetch_assoc())
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
        $query = $this->GetConnection()->prepare(
            "INSERT INTO FEED_EVENTS (ReclineeID, Text, Timestamp, Image) VALUES ('$reclineeID', '$_text', '$timestamp', '$image')"
        );
        $query->bind_param('isis', $reclineeID, $_text, date('U'), $image);
        $result = $this->Query($query);
        if(!$result)
            return FALSE;
		
		$feedEvent = new FEED_EVENT();
		$feedEvent->LoadFromArgs(
            $this->GetConnection()->insert_id, $reclineeID, $text, $timestamp, $image
        );
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