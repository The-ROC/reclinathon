<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

include '../RECLINATHON_CONTEXT.php';

    if(isset($_GET["feedPost"]))
    {
        $feedPost = $_GET["feedPost"];
        $postTime = time();
    }
?>

<HTML>
<HEAD>
<title>Reclinathon Tracking Technology</title>
<link rel="stylesheet" type="text/css" href="mockup.css" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="flickity.css" media="screen">
</HEAD>

<BODY bgcolor='white' CLASS='noborder'>
    <script src="date.format.js"></script>
    <script src="flickity.pkgd.js"></script>

    <script>
        function setCountdownTimer(milliseconds)
        {
            this.countdownToTime(new Date().getTime() + milliseconds);
        }

        function countdownToTime(countdownTimeMS)
        {
            var x = setInterval(function()
            {
                var now = new Date().getTime();
                var distance = countdownTimeMS - now;
                
                var countdownDiv = this.displayTimer(distance);

                if(distance < 0) {
                    clearInterval(x);
                    countdownDiv.innerHTML = "Merry Reclinathon!";
					window.location.href = "feed.php?advance=1";
                }
            }, 200);
        }

        function displayTimer(distanceMS)
        {
            var days = Math.floor(distanceMS / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distanceMS % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distanceMS % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distanceMS % (1000 * 60)) / 1000);
                
                var countdownDiv = document.getElementById("countdown");
                countdownDiv.innerHTML = "";
                if(days == 1)
                    countdownDiv.innerHTML += days + " day<br/>";
                else if(days > 1)
                    countdownDiv.innerHTML += days + " days<br/>";
                countdownDiv.innerHTML += pad(hours, 2) + ":" + pad(minutes, 2) + ":" + pad(seconds, 2);

            return countdownDiv;
        }

        function pad(num, digits)
        {
            var s = num + "";
            while(s.length < digits)
                s = "0" + s;
            return s;
        }

        function getDateFromTimestamp(timestamp)
        {
            var postDate = new Date(timestamp*1000);
            var currentDate = new Date();

            var postDay = new Date(postDate.format('mm/dd/yyyy'));
            var currentDay = new Date(currentDate.format('mm/dd/yyyy'));
            var timeDiff = currentDay.getTime() - postDay.getTime();
            var daysDiff = Math.floor(timeDiff / (1000*3600*24));

            if(daysDiff == 0)
            {
                var msAgo = Math.max(0, currentDate.getTime() - postDate.getTime());
                var hoursAgo = Math.floor(msAgo / (1000*3600));
                var minutesAgo = Math.floor(msAgo / (1000*60));
                if(hoursAgo == 0)
                {
                    if(minutesAgo == 0)
                        return "Just now";
                    else if(minutesAgo == 1)
                        return minutesAgo + " min";
                    else
                        return minutesAgo + " mins";
                }
                else
                {
                    if(hoursAgo == 1)
                        return hoursAgo + " hr";
                    else
                        return hoursAgo + " hrs";
                }
            }
            else if(daysDiff == 1)
            {
                return postDate.format('"Yesterday at" h:MMtt');
            }
            else if(postDate.getYear() == currentDate.getYear()) 
            {
                return postDate.format('mmmm d "at" h:MMtt');
            }
            else
            {
                return postDate.format('mmmm d, yyyy "at" h:MMtt');
            }
        }

        function addFeedEvent(icon, message, timestamp)
        {
            var postsParent = document.getElementById("postsParent");

            var newPostHTML = "<div class='container' style='padding:5px' timestamp='" + timestamp + "'>";
            newPostHTML += "<div class='content'><img src='" + icon + "' height='50' width='50'/></div>";
            newPostHTML += "<div class='content' style='text-align:left; padding-left:15px'>";
            newPostHTML += "<div class='container'>" + message + "</div>";
            newPostHTML += "<div class='container' style='font-size:50%' date='true'>" + getDateFromTimestamp(timestamp) + "</div>";
            newPostHTML += "</div>";
            newPostHTML += "</div>";

            postsParent.innerHTML = newPostHTML + postsParent.innerHTML;
        }

        function updateFeedDates()
        {
            var postsParent = document.getElementById("postsParent");
            var posts = postsParent.childNodes;
            for(i=0; i<posts.length; i++)
            {
                var post = posts[i];
                var timestamp = post.getAttribute("timestamp");
                var postChildren = post.getElementsByTagName("div");
                for(j=0; j<postChildren.length; j++)
                {
                    var postChild = postChildren[j];
                    if(postChild.hasAttribute("date"))
                        postChild.innerHTML = getDateFromTimestamp(timestamp);
                }
            }
        }

        function onPostClick()
        {
            var feedPost = document.getElementById("feedPost");
            var message = feedPost.value;
            feedPost.value = "";
            var userId = "dude";

            var xhReq = createXMLHttpRequest();
            xhReq.open("GET", "feedpost.php?user=" + userId + "&feedPost=" + message);
            xhReq.onreadystatechange = function() {
                if (xhReq.readyState != 4) return;
		
                var xml = xhReq.responseXML;

                var feedEvents = xml.getElementsByTagName("FeedEvents");
                for(var i = 0; i < feedEvents.length; i++)
                {
                    var icon = feedEvents[i].getElementsByTagName("Icon")[0].childNodes[0].nodeValue;
                    var message = feedEvents[i].getElementsByTagName("Message")[0].childNodes[0].nodeValue;
                    var timestamp = feedEvents[i].getElementsByTagName("Timestamp")[0].childNodes[0].nodeValue;
                    
                    addFeedEvent(icon, message, timestamp);
                    updateFeedDates();
                }
            }
            xhReq.send();
        }

        function onLoginClick()
        {
            document.getElementById("loginPanel").style.display = "none";
            document.getElementById("postPanel").style.display = "block";
        }

        function createXMLHttpRequest()
        {
	        try { return new XMLHttpRequest(); } catch(e) {}
	        try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	        alert("XMLHttpRequest not supported");
	        return null;
        }
    </script>
	
	<CENTER>
    <div id="stateSummary" class="header">

    <?php

	echo $_GET["refreshTime"] . "<br>";
    $remoteReclinathon = new REMOTE_RECLINATHON();
    $currentReclinathonId = $remoteReclinathon->GetCurrentRemoteReclinathonId();
    $remoteReclinathonScheduled = $currentReclinathonId != "";
	$rcx = new RECLINATHON_CONTEXT();

	if ($remoteReclinathonScheduled)
	{
		$contextId = $_GET["contextId"];
		if ($contextId == "")
		{		
			if (!$rcx->LoadCurrentNonPending($currentReclinathonId))
			{
				echo "No state has been logged for this Reclinathon. <br>";
			}
		}
		else
		{
			if (!$rcx->Load($contextId))
			{
				echo "Context $contextId not found. <br>";
			}
		}
	}

	if ($_GET["advance"] == 1)
	{
		$rcx->Advance();
		$currentReclinathonId = $remoteReclinathon->GetCurrentRemoteReclinathonId();
        $remoteReclinathonScheduled = $currentReclinathonId != "";
		
		if ($remoteReclinathonScheduled)
	    {
		    $rcx->LoadCurrentNonPending($currentReclinathonId);
		}
	}

	if ($remoteReclinathonScheduled)
	{
		$rcx->DisplayFeedModule();
		
		echo "<div class='container' style='text-align:center'>
			  <button class='button' onclick=\"window.location.href = 'https://www.netflix.com/watch/70083111?t=52';\">Join the Reclinathon</button>
			  <div class='container' style='height:10px'>&nbsp;</div>
			  </div>";
			  
		echo "<button class='button' onclick=\"window.location.href = 'feed.php?advance=1';\">Advance</button>";
	}			
	else
	{
		echo "<div id='upNext' class='container' style='padding:15px 0'>
			  <div class='container' style='height:65px'><img src='images/sign.png' width='360'/></div>
			  <div class='container' style='height:40px'>No Events Scheduled</div>
			  <div class='container' style='text-align:center'>
				  <form style='margin-bottom:0'><button class='button' type='submit' formaction='create_reclinathon.php'>Create a Reclinathon</button></form>
			  </div>
			  </div>";
	}
	
    ?>

    </div>
	
<!--
    Feed section
-->
    <div id="feed" style="width:100%; text-align:left">
    <div id="postPanel" class="container" style="padding:5px; width:100%; box-sizing: border-box; background-color:#eeeeff; display:none">
            <div style="padding-bottom: 15px">
                <div class="content"><img src="images/reclinathon.jpg" height="50" width="50"/></div>
                <div class="content" style="text-align:left; padding-left:15px; width: 100%; box-sizing: border-box">
                    <div class="container" style="width:100%"><input type="text" id="feedPost" name="feedPost" placeholder="Post something!" style="width:100%"></div>
                    <div class="container"><button onclick="onPostClick();">Post</button></div>
                </div>
            </div>
        </div>

        <div id="loginPanel" class="container" style="padding:5px; width:100%; box-sizing: border-box; background-color:#9999BB">
            <div style="padding-bottom: 15px; text-align:left;">
                <div class="container" style="margin:auto">
                <div class="content" style="padding-right:15px">
                    <div class="container">Username</div>
                    <div class="container"><input type="text" id="username" size="12"></div>
                </div>
                <div class="content" style="padding-right:15px">
                    <div class="container">Password</div>
                    <div class="container"><input type="password" id="password" size="12"></div>
                </div>
                <!-- <div class="container" style="width:100%; padding-top:10px"> -->
                <div class="content">
                    <div class="container">&nbsp</div>
                    <div class="container"><button onclick="onLoginClick();">Login</button></div>
                </div>
                <!--</div>-->
                </div>
            </div>
        </div>

        <div id='postsParent' class='container'></div>

		<?php
		
		if ($finishedKillBill)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='images/finish.png' height='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Completed a Mini Reclinathon!</div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 7:10pm</div>";
            echo "</div>";
            echo "</div>";
			
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content'><img src='images/downtime.png' height='50' width='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Finished <a href='../index.php?ContextID=468'>Kill Bill</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 7:10pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		if ($startedKillBill)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='https://m.media-amazon.com/images/M/MV5BNzM3NDFhYTAtYmU5Mi00NGRmLTljYjgtMDkyODQ4MjNkMGY2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg' height='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Started <a href='../index.php?ContextID=468'>Kill Bill</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 4:43pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		if ($finishedInBruges)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content'><img src='images/downtime.png' height='50' width='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Finished <a href='../index.php?ContextID=468'>In Bruges</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 4:30pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		if ($startedInBruges)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='https://m.media-amazon.com/images/M/MV5BMTUwOGFiM2QtOWMxYS00MjU2LThmZDMtZDM2MWMzNzllNjdhXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg' height='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Started <a href='../index.php?ContextID=468'>In Bruges</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 2:30pm</div>";
            echo "</div>";
            echo "</div>";
			
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='images/play.png' height='50' width='50' /></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Started a Mini Reclinathon!</div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 2:15pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		?>
		
        <div class="container" style="padding:5px">
            <div class="content"><img src="images/downtime.png" height="50" width="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Finished <a href="../index.php?ContextID=468">Mad Max: Fury Road</a></div>
                <div class="container" style="font-size:50%">June 17 at 11:11am</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content"><img src="images/dave.jpg" height="50" width="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Mad Max Fury Road just broke the Guinness World Record for longest movie ever, previously held by Logistics.</div>
                <div class="container" style="font-size:50%">June 17 at 10:30am</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content" style="width:50px; text-align:center"><img src="http://upload.wikimedia.org/wikipedia/en/6/6e/Mad_Max_Fury_Road.jpg" height="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Started <a href='../index.php?ContextID=468'>Mad Max: Fury Road </a></div>
                <div class="container" style="font-size:50%">May 12 at 5:30pm</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content"><img src="images/downtime.png" height="50" width="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Finished <a href="../index.php?ContextID=465">The Legend of Old Gregg</a></div>
                <div class="container" style="font-size:50%">December 28, 2017 at 3:49pm</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content" style="width:50px; text-align:center"><img src="images/finish.png" height="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Completed a Full Reclinathon!</div>
                <div class="container" style="font-size:50%">December 28, 2017 at 3:28pm</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content" style="width:50px; text-align:center"><img src="https://upload.wikimedia.org/wikipedia/en/d/d8/Boosh_s2.gif" height="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Started <a href="../index.php?ContextID=465">The Legend of Old Gregg</a></div>
                <div class="container" style="font-size:50%">December 28, 2017 at 3:22pm</div>
            </div>
        </div>
    </div>
</CENTER>
</BODY>
</HTML>