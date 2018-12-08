<?php
session_start();

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

include '../RECLINATHON_CONTEXT.php';
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
        var reclineeId = <?php echo '"' . $_SESSION['ReclineeID'] . '";'; ?>
        var lastEventId = 0;

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
					advance();
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
            newPostHTML += "<div class='content'><img src='" + icon + "' width='50'/></div>";
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

            this.updateFeedEvents(message);
        }

        function updateFeedEvents(message)
        {
            var xhReq = createXMLHttpRequest();
            var params = "";

            if(message !== undefined)
            {
                xhReq.open("POST", "feedpost.php?lastEventID=" + lastEventId);
                params = "reclineeID=" + reclineeId + "&feedPost=" + encodeURIComponent(message);
                xhReq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            }
            else
            {
                xhReq.open("GET", "feedpost.php?lastEventID=" + lastEventId);
            }

            xhReq.overrideMimeType('text/xml');
            xhReq.onreadystatechange = function() {
                if (xhReq.readyState != 4) return;
		
                var xml = xhReq.responseXML;

                var feedEvents = xml.getElementsByTagName("FeedEvent");
                for(var i = 0; i < feedEvents.length; i++)
                {
                    var icon = feedEvents[i].getElementsByTagName("Icon")[0].childNodes[0].nodeValue;
                    var message = feedEvents[i].getElementsByTagName("Message")[0].childNodes[0].nodeValue;
                    var timestamp = feedEvents[i].getElementsByTagName("Timestamp")[0].childNodes[0].nodeValue;
                    var eventId = parseInt(feedEvents[i].getElementsByTagName("EventID")[0].childNodes[0].nodeValue);

                    if(eventId > lastEventId)
                        lastEventId = eventId;
                        
                    addFeedEvent(icon, message, timestamp);
                    updateFeedDates();
                }
            }
            xhReq.send(params);
        }

        function init()
        {
            // Start event polling loop
            this.updateFeedEvents();
            setInterval(this.updateFeedEvents, 3000);

            // Add an enter key event listener to the text input field
            document.getElementById("feedPost").addEventListener("keyup", function(event) {
                event.preventDefault();
                if(event.keyCode === 13) {
                    document.getElementById("postButton").click();
                }
            });
        }

        function onLoginClick()
        {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;

            var xhReq = createXMLHttpRequest();
            xhReq.open("POST", "../../dologin.php", true);
            xhReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhReq.onreadystatechange = function() {
                if (xhReq.readyState != 4) return;

                var xml = xhReq.responseXML;
                var login = xml.getElementsByTagName("Login")[0];
                var result = login.getAttribute("result");
                if(result == "success")
                {
                    reclineeId = login.getAttribute("username");
                }
                onLoginChange();
            }
            xhReq.send("username=" + username + "&password=" + password + "&xml=true");
        }

        function onLoginChange()
        {
            document.getElementById("username").value = "";
            document.getElementById("password").value = "";
            if(reclineeId != "")
                document.getElementById("postImage").src = "images/" + reclineeId + ".png";

            if(reclineeId)
            {
                document.getElementById("loginPanel").style.display = "none";
                document.getElementById("postPanel").style.display = "block";
            }
            else
            {
                document.getElementById("loginPanel").style.display = "block";
                document.getElementById("postPanel").style.display = "none";
            }
        }
		
		function advance()
        {
			var contextId = document.getElementById("currentContextId").value;
			
			if (contextId == "" || contextId == "0")
			{
				return;
			}
			
            var xhReq = createXMLHttpRequest();
            xhReq.open("GET", "advanceReclinathon.php?contextId=" + contextId);
            xhReq.onreadystatechange = function() {
                if (xhReq.readyState != 4) return;
		
                window.location.reload(true);
            }
            xhReq.send();
        }
		
		function getReclinathonState(callback)
		{
			var xhReq = createXMLHttpRequest();
            xhReq.open("GET", "getReclinathonState.php");
            xhReq.onreadystatechange = function() {
                    if (xhReq.readyState != 4) return;
					
					var result = {};
					result.contextId = 0;
			        result.url = "";
			
		            var xml = xhReq.responseXML;
					
					var contextIdNodes = xml.getElementsByTagName("Context");
			        if (contextIdNodes.length > 0)
			        {
				        result.contextId = contextIdNodes[0].getAttribute("id");
				        result.url = contextIdNodes[0].getAttribute("url");
			        }
					
					callback(result);
			    }
            xhReq.send();
		}
		
		function checkForReclinathonContextChange()
        {
			var callback = function(result) {
			    var localContextId = document.getElementById("currentContextId").value;
		        var actualContextId = result.contextId;
			
			    if (actualContextId != "" && actualContextId != "0" && localContextId != actualContextId)
			    {
				    window.location.reload(true);
			    }
			};
			
			getReclinathonState(callback);
        }
		
		function joinReclinathon()
		{
			var callback = function(result) {		
			    if (result.url != "")
			    {
			        window.location.href = result.url;
			    }
			};
			
			getReclinathonState(callback);
		}

        function createXMLHttpRequest()
        {
	        try { return new XMLHttpRequest(); } catch(e) {}
	        try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	        alert("XMLHttpRequest not supported");
	        return null;
        }
    </script>
	
	<input type='hidden' name='extensionActiveInput' id='extensionActiveInput' value='false' />
	
	<CENTER>
    <div id="stateSummary" class="header">

    <?php
	
    $remoteReclinathon = new REMOTE_RECLINATHON();
    $currentReclinathonId = $remoteReclinathon->GetCurrentRemoteReclinathonId();
    $remoteReclinathonScheduled = $currentReclinathonId != "";
	$rcx = new RECLINATHON_CONTEXT();
	$currentContextId = 0;

	if ($remoteReclinathonScheduled)
	{
		$contextId = isset($_GET["contextId"]) ? $_GET["contextId"] : "";
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
		
		$currentContextId = $rcx->GetContextId();
	}
	
	echo "<input type='hidden' id='currentContextId' name='currentContextId' value='$currentContextId' />";

	if ($remoteReclinathonScheduled)
	{
		$rcx->DisplayFeedModule();
		
		$url = $rcx->GetUrl();	
		$joined = isset($_GET["joined"]) ? $_GET["joined"] : "0";

		if ($url != "" && $joined != "1")
		{
		    echo "<div class='container' style='text-align:center'>
			      <button class='button' onclick=\"joinReclinathon();\">Join the Reclinathon</button>
			      <div class='container' style='height:10px'>&nbsp;</div>
			      </div>";
		}
		
        $adminMode = isset($_GET["admin"]) ? $_GET["admin"] : "0";
		if ($adminMode == "1")
		{
		    echo "<button class='button' onclick=\"advance();\">Advance</button>";
		}
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
                <div class="content"><img id="postImage" src=<?php echo "images/reclinathon.jpg";?> width="50"/></div>
                <div class="content" style="text-align:left; padding-left:15px; width: 100%; box-sizing: border-box">
                    <div class="container" style="width:100%"><input type="text" id="feedPost" name="feedPost" placeholder="Post something!" style="width:100%"></div>
                    <div class="container"><button id="postButton" onclick="onPostClick();">Post</button></div>
                </div>
            </div>
        </div>

        <div id="loginPanel" class="container" style="padding:5px; width:100%; box-sizing: border-box; background-color:#9999BB; display:none">
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
                <div class="content">
                    <div class="container">&nbsp</div>
                    <div class="container"><button onclick="onLoginClick();">Login</button></div>
                </div>
                </div>
            </div>
        </div>

        <script>
            this.init();
            this.onLoginChange();
        </script>

        <div id='postsParent' class='container'></div>
    </div>
</CENTER>
</BODY>
</HTML>