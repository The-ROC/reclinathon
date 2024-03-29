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

        function updateScriptDateFromTimestamp(timestamp)
        {
            var scriptTag = document.scripts[document.scripts.length - 1].parentNode;
            scriptTag.innerHTML += getDateFromTimestamp(timestamp);
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

<?php

$startedInBruges = false;
$finishedInBruges = false;
$startedKillBill = false;
$finishedKillBill = false;

?>

<!-- Logo
<DIV CLASS='title'>
    <CENTER>
        <IMG SRC='images/DefaultLogo.png'>
    </CENTER>
</DIV>
-->

<CENTER>
<!-- Watch Live Button
    <A HREF='http://www.reclinathon.com/watch.php'>Watch Live</A>
    <BR>
    <BR>
-->
    <div id="stateSummary" class="header">

        <?php 
            if($_GET["activity"] === "Scheduled") {
        ?>
            
        <div id="scheduledReclinathon" class="container" style="padding:15px 0; width:100%">
            <div id="nowPlayingText" class="container"><div class="content"><b>Countdown to Reclinathon!</b></div></div>
            <div id="timeRemaining" class="container"><div id="countdown" class="content"><script>this.displayTimer(2*60*1000); this.setCountdownTimer(2*60*1000);</script></div></div>
            <div id="timeRemaining" class="container" style="height:10px"><div class="content"></div></div>

            <div class="main-carousel" data-flickity='{ "cellAlign": "center", "contain": false}' style="width:100%;background-image:url('film.png'); background-size: 300px 150px">
                <div class="carousel-cell">
                    <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BMTUwOGFiM2QtOWMxYS00MjU2LThmZDMtZDM2MWMzNzllNjdhXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg" alt = 'In Bruges ' />
                </div>
                <div class="carousel-cell">
                    <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BNzM3NDFhYTAtYmU5Mi00NGRmLTljYjgtMDkyODQ4MjNkMGY2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg" alt = 'Kill Bill: Vol. 1 ' />
                </div>
                <div class="carousel-cell">
                    <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BMTUwOGFiM2QtOWMxYS00MjU2LThmZDMtZDM2MWMzNzllNjdhXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg" alt = 'In Bruges ' />
                </div>
                <div class="carousel-cell">
                    <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BNzM3NDFhYTAtYmU5Mi00NGRmLTljYjgtMDkyODQ4MjNkMGY2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg" alt = 'Kill Bill: Vol. 1 ' />
                </div>
                <div class="carousel-cell">
                    <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BMTUwOGFiM2QtOWMxYS00MjU2LThmZDMtZDM2MWMzNzllNjdhXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg" alt = 'In Bruges ' />
                </div>
                <div class="carousel-cell">
                    <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BNzM3NDFhYTAtYmU5Mi00NGRmLTljYjgtMDkyODQ4MjNkMGY2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg" alt = 'Kill Bill: Vol. 1 ' />
                </div>
            </div>
            <div id="timeRemaining" class="container" style="height:25px"><div class="content"></div></div>
        </div>


        <?php 
            } else if($_GET["activity"] === "Reclining1" || $_GET["activity"] === "join")
			{
				$startedInBruges = true;
        ?>

        <div id="nowPlaying" class="container" style="padding:15px">
            <div id="nowPlayingPoster" class="content" style="text-align:right">
                <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BMTUwOGFiM2QtOWMxYS00MjU2LThmZDMtZDM2MWMzNzllNjdhXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg" alt = 'In Bruges ' />
            </div>
            <div class="content" style="width:20px"></div>
            <div id="nowPlayingInfo" class="content" style="text-align:left">
                <div id="nowPlayingText" class="container" style="height:40px"><div class="content"><b>Now Playing</b></div></div>
                <div id="movieTitle" class="container" style="height:40px"><div class="content">In Bruges</div></div>
                <div id="timeRemaining" class="container" style="height:40px"><div class="content">10:00:00</div></div>
            </div>
        </div>

        <?php
                if ($_GET["activity"] === "join")
			    {
			        echo "<div class='container' style='text-align:center'>";
                    echo "<button class='button' onclick=\"window.location.href = 'https://www.netflix.com/watch/70083111?t=52';\">Join the Reclinathon</button>";
					echo "<div class='container' style='height:10px'>&nbsp;</div>";
                    echo "</div>";
			    }
			}
			else if($_GET["activity"] === "Downtime") { $startedInBruges = true; $finishedInBruges = true;
        ?>

        <div id="upNext" class="container" style="padding:15px">
            <div id="upNextPoster" class="content" style="text-align:right">
                <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BNzM3NDFhYTAtYmU5Mi00NGRmLTljYjgtMDkyODQ4MjNkMGY2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg" alt = 'Kill Bill: Vol. 1 ' />
            </div>
            <div class="content" style="width:20px"></div>
            <div id="upNextInfo" class="content" style="text-align:left">
                <div id="upNextText" class="container" style="height:40px"><div class="content"><b>Up Next</b></div></div>
                <div id="movieTitle" class="container" style="height:40px"><div class="content">Kill Bill: Vol. 1</div></div>
                <div id="timeRemaining" class="container" style="height:40px"><div class="content">10:00:00</div></div>
            </div>
        </div>

        <?php
            } else if($_GET["activity"] === "Reclining2") { $startedInBruges = true; $finishedInBruges = true; $startedKillBill = true;
        ?>

        <div id="nowPlaying" class="container" style="padding:15px">
            <div id="nowPlayingPoster" class="content" style="text-align:right">
                <img border='3' height='150' src="https://m.media-amazon.com/images/M/MV5BNzM3NDFhYTAtYmU5Mi00NGRmLTljYjgtMDkyODQ4MjNkMGY2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg" alt = 'Kill Bill: Vol. 1 ' />
            </div>
            <div class="content" style="width:20px"></div>
            <div id="nowPlayingInfo" class="content" style="text-align:left">
                <div id="nowPlayingText" class="container" style="height:40px"><div class="content"><b>Now Playing</b></div></div>
                <div id="movieTitle" class="container" style="height:40px"><div class="content">Kill Bill: Vol. 1</div></div>
                <div id="timeRemaining" class="container" style="height:40px"><div class="content">10:00:00</div></div>
            </div>
        </div>
    
        <?php
            }
			
			else if ($_GET["activity"] === "EndReclinathon")
			{
				$startedInBruges = true;
				$finishedInBruges = true;
				$startedKillBill = true;
				$finishedKillBill = true;
				$showCreateButton = true;
			}
			else
			{
				$showCreateButton = true;
			}

		    if ($showCreateButton)
		    {
			
		?>
		
        <div id="upNext" class="container" style="padding:15px 0">
        <div class="container" style="height:65px"><img src="images/sign.png" width="360"/></div>
        <div class="container" style="height:40px">No Events Scheduled</div>
            <div class="container" style="text-align:center">
                <form style="margin-bottom:0"><button class="button" type="submit" formaction="create_reclinathon.php">Create a Reclinathon</button></form>
            </div>
        </div>

        <?php
            }
        ?>
    </div>
<!--
    <div class="timeRemaining">
        <br/>
    </div>
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

        if($feedPost)
        {
            echo "<div class='container' style='padding:5px'>";
            echo "<div class='content'><img src='images/reclinathon.jpg' height='50' width='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>" . $feedPost . "</div>";
            echo "<div class='container' style='font-size:50%'><script>updateScriptDateFromTimestamp(" . $postTime . ");</script></div>";
            echo "</div>";
            echo "</div>";
        }
		
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

    <TABLE CLASS='RttFrameRight'>
    <!--
        <TR>
            <TD CLASS='RttFrameRight'><FORM NAME='sw'>
                <TABLE>
    -->
    <!--
                    <TR cellspacing='0'>
                        <TH>Reclining</TH>
                        <th></th>
                        <TH CLASS='right'>05/12/2018 5:30 PM</TH>
                    </TR><TR>
                        <TD>Movie:</TD>
                        <TD>Mad Max: Fury Road</td>
                        <td> <IMG BORDER='3' WIDTH='200' SRC = 'http://upload.wikimedia.org/wikipedia/en/6/6e/Mad_Max_Fury_Road.jpg' alt = 'Mad Max: Fury Road ' ></TD>
                    </TR><TR>
                        <TD>Time Remaining:*</TD>
                        <TD><input type='text' name='disp2' size='18' style="border: 0px; background-color: #C6D9F1; font-size: 125%;" readonly></INPUT></TD>
                        <td></td>
                    </TR><TR>
                        <TD>Captain:</TD>
                        <TD>Dude</TD>
                        <td></td>
                    </TR>
-->
    <!--
                </TABLE></FORM>
            </TD>
    -->
<!-- Entertainment cell
            <TD CLASS='RttFrameRight'>
                <TABLE>
                    <TR cellspacing='0'>
                        <TH>Entertainment</TH>
                    </TR><TR>
                        <TD ID='TriviaQuestion'></TD>
                    </TR><TR>
                        <TD ID='TriviaAnswer'></TD>
                    </TR>
                </TABLE>
            </TD>
-->
<!-- Spacer row
        </TR><TR>
            <TD CLASS='RttFrame'><BR></TD>
            <TD CLASS='RttFrameRight'><BR></TD>
-->
<!--
        </TR><TR>
-->
<!-- Movie detail cell
            <TD CLASS='RttFrame'>
                <TABLE>
                    <TR cellspacing='0'>
                        <TH>Mad Max: Fury Road  (2015)</TH>
                        <TH CLASS='right'>97%(90)</TH>
                    </TR><TR ID='movie782'>
                        <TD><IMG BORDER='3' WIDTH='200' SRC = 'http://upload.wikimedia.org/wikipedia/en/6/6e/Mad_Max_Fury_Road.jpg' alt = 'Mad Max: Fury Road ' ><BR><BR><B><U><FONT SIZE='+2'>Synopsis:</FONT></U></B><BR>A woman rebels against a tyrannical ruler in postapocalyptic Australia in search for her home-land with the help of a group of female prisoners, a psychotic worshipper, and a drifter named Max.</TD>
                        <TD><B><U><FONT SIZE='+2'>Genre(s):</FONT></U></B><BR><B> Action</B><BR><B> Thriller</B><BR><B> Sci-Fi</B><BR><B> Adventure</B><BR><B>Apocalypse</B><BR><B>Sequel</B><BR>Reclinathon Theme<BR><B>Aussie as, mate!</B><BR><B>Who runs Bartertown?</B><BR><B>Flamethrower guitars</B><BR><BR><B><U><FONT SIZE='+2'>Runtime:</FONT></U></B><BR>120 min<BR><BR><B><U><FONT SIZE='+2'>Cast and Crew:</FONT></U></B><BR>Director: George Miller<BR><BR>Tom Hardy<BR>Charlize Theron<BR>Nicholas Hoult<BR>Hugh Keays-Byrne<BR>Josh Helman<BR>Nathan Jones<BR>Zo? Kravitz<BR>Rosie Huntington-Whiteley<BR>John Howard<BR>Iota<BR>Megan Gale<BR><BR><BR><A HREF='http://www.imdb.com/title/tt1392190/' target='_blank'>IMDB</A><BR><A HREF = 'http://www.youtube.com/results?search_query=Mad Max: Fury Road trailer, hd, short' target='_blank'>Trailer</A></TD>
                    </TR>
                </TABLE>
            </TD>
-->
<!--
            <TD CLASS='RttFrameRight'>
                <TABLE>
-->
<!--
                    <TR>
                        <TH>History</TH>
                        <th></th>
                        <TH CLASS='right'><A HREF = 'index.php'>Go To Current</A></TH>
                    </TR>
-->
<!--
                    <TR>
                        <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=466'>Postseason</A></TD>
                        <TD align='right'>12/28/2017 3:49 PM</TD>
                    </TR><TR>
                        <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=465'>The Legend of Old Gregg</A></TD>
                        <TD align='right'>12/28/2017 3:22 PM</TD>
                    </TR><TR>
                        <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=464'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 3:16 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=463'>The Lego Batman Movie</A></TD>
                        <TD align='right'>12/28/2017 1:31 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=462'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 1:15 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=461'>Passengers </A></TD>
                        <TD align='right'>12/28/2017 11:21 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=460'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 11:08 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=459'>Logan </A></TD>
                        <TD align='right'>12/28/2017 8:48 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=458'>Emergency Maintenance</A></TD>
                        <TD align='right'>12/28/2017 8:44 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=457'>Logan </A></TD>
                        <TD align='right'>12/28/2017 8:43 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=456'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 8:23 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=455'>Office Christmas Party</A></TD>
                        <TD align='right'>12/28/2017 6:38 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=454'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 6:37 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=453'>Kingsman: The Golden Circle</A></TD>
                        <TD align='right'>12/28/2017 4:16 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=452'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 4:09 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=451'>Get Out </A></TD>
                        <TD align='right'>12/28/2017 2:25 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=450'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 2:16 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=449'>Masterminds</A></TD>
                        <TD align='right'>12/28/2017 12:42 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=448'>Downtime</A></TD>
                        <TD align='right'>12/28/2017 12:30 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=447'>Doctor Strange </A></TD>
                        <TD align='right'>12/27/2017 10:36 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=446'>Emergency Maintenance</A></TD>
                        <TD align='right'>12/27/2017 10:30 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=445'>Doctor Strange </A></TD>
                        <TD align='right'>12/27/2017 10:27 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=444'>Downtime</A></TD>
                        <TD align='right'>12/27/2017 10:03 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=443'>Arrival</A></TD>
                        <TD align='right'>12/27/2017 8:08 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=442'>Downtime</A></TD>
                        <TD align='right'>12/27/2017 7:39 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=441'>The Big Sick </A></TD>
                        <TD align='right'>12/27/2017 5:40 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=440'>Downtime</A></TD>
                        <TD align='right'>12/27/2017 5:18 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=439'>Dunkirk</A></TD>
                        <TD align='right'>12/27/2017 3:34 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=438'>Downtime</A></TD>
                        <TD align='right'>12/27/2017 3:16 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=437'>Mad Max: Fury Road </A></TD>
                        <TD align='right'>12/27/2017 1:16 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=436'>Preseason:Final Countdown</A></TD>
                        <TD align='right'>12/26/2017 1:00 PM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=435'>Preseason:Election</A></TD>
                        <TD align='right'>12/11/2017 12:00 AM</TD>
                    </TR><TR>
                    <TD></TD>
                        <TD><A HREF = 'index.php?ContextID=434'>Preseason:Preparing the Ballots</A></TD>
                        <TD align='right'>12/10/2017 12:00 AM</TD>
                    </TR>
-->
<!--
                </TABLE>
            </TD>
        </TR>
-->
    </TABLE>
</CENTER>
</BODY>
</HTML>