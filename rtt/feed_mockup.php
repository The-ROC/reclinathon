<!--
Feed ideas.
 - There is no reclinathon, start one.
 - Join relcinathon button.
 - Get extension state.
-->

<HTML>
<HEAD>
<title>Reclinathon Tracking Technology</title>
<link rel="stylesheet" type="text/css" href="mockup.css" />
<meta name="viewport" content="width=device-width, initial-scale=1">
</HEAD>

<BODY bgcolor='white' CLASS='noborder'>

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
            if($_GET["activity"] === "Reclining1" || $_GET["activity"] === "join")
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
        <div class="container" style="height:65px"><img src="sign.png" width="360"/></div>
        <div class="container" style="height:40px">No Events Scheduled</div>
            <div class="container" style="text-align:center">
                <button class="button">Create a Reclinathon</button>
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
        <div class="container" style="padding:5px; width:100%; box-sizing: border-box">
        <form>
            <div class="content"><img src="reclinathon.jpg" height="50" width="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px; width: 100%; box-sizing: border-box">
                <div class="container" style="width:100%"><input type="text" name="feedPost" value="Post something!" style="width:100%"></div>
                <div class="container"><input type="submit" name="submit" value="Post"></div>
            </div>
        </form>
        </div>

		<?php
		
		if ($finishedKillBill)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='finish.png' height='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Completed a Mini Reclinathon!</div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 7:10pm</div>";
            echo "</div>";
            echo "</div>";
			
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content'><img src='downtime.png' height='50' width='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Finished <a href='index.php?ContextID=468'>Kill Bill</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 7:10pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		if ($startedKillBill)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='https://m.media-amazon.com/images/M/MV5BNzM3NDFhYTAtYmU5Mi00NGRmLTljYjgtMDkyODQ4MjNkMGY2XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg' height='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Started <a href='index.php?ContextID=468'>Kill Bill</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 4:43pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		if ($finishedInBruges)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content'><img src='downtime.png' height='50' width='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Finished <a href='index.php?ContextID=468'>In Bruges</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 4:30pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		if ($startedInBruges)
		{
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='https://m.media-amazon.com/images/M/MV5BMTUwOGFiM2QtOWMxYS00MjU2LThmZDMtZDM2MWMzNzllNjdhXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg' height='50'/></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Started <a href='index.php?ContextID=468'>In Bruges</a></div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 2:30pm</div>";
            echo "</div>";
            echo "</div>";
			
			echo "<div class='container' style='padding:5px'>";
            echo "<div class='content' style='width:50px; text-align:center'><img src='play.png' height='50' width='50' /></div>";
            echo "<div class='content' style='text-align:left; padding-left:15px'>";
            echo "<div class='container'>Started a Mini Reclinathon!</div>";
            echo "<div class='container' style='font-size:50%'>August 12 at 2:15pm</div>";
            echo "</div>";
            echo "</div>";
		}
		
		?>
		
        <div class="container" style="padding:5px">
            <div class="content"><img src="downtime.png" height="50" width="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Finished <a href="index.php?ContextID=468">Mad Max: Fury Road</a></div>
                <div class="container" style="font-size:50%">June 17 at 11:11am</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content"><img src="dave.jpg" height="50" width="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Mad Max Fury Road just broke the Guinness World Record for longest movie ever, previously held by Logistics.</div>
                <div class="container" style="font-size:50%">June 17 at 10:30am</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content" style="width:50px; text-align:center"><img src="http://upload.wikimedia.org/wikipedia/en/6/6e/Mad_Max_Fury_Road.jpg" height="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Started <a href='index.php?ContextID=468'>Mad Max: Fury Road </a></div>
                <div class="container" style="font-size:50%">May 12 at 5:30pm</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content"><img src="downtime.png" height="50" width="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Finished <a href="index.php?ContextID=465">The Legend of Old Gregg</a></div>
                <div class="container" style="font-size:50%">December 28, 2017 at 3:49pm</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content" style="width:50px; text-align:center"><img src="finish.png" height="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Completed a Full Reclinathon!</div>
                <div class="container" style="font-size:50%">December 28, 2017 at 3:28pm</div>
            </div>
        </div>
        <div class="container" style="padding:5px">
            <div class="content" style="width:50px; text-align:center"><img src="https://upload.wikimedia.org/wikipedia/en/d/d8/Boosh_s2.gif" height="50"/></div>
            <div class="content" style="text-align:left; padding-left:15px">
                <div class="container">Started <a href="index.php?ContextID=465">The Legend of Old Gregg</a></div>
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