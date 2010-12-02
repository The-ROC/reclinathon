<?php 

session_name("reclinathon2");
session_id("reclinathon2");
session_Start();

include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";

if(!session_is_registered("GO")) {
  $GO = 0;
  session_register("GO");
}


//while($GO == 0) {
//}

//$RAND = rand(1, $RANDMAX);

if($MODE != 1) {
  $query = "SELECT * FROM Trivia WHERE TID = ".$RAND;
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  $refresh = 15;
}
else {
  $query = "SELECT * FROM VideoClips WHERE VCID = ".$RAND_CLIP;
  $result = mysql_query($query);
  $row = mysql_fetch_row($result);
  $refresh = $row[2];
  echo $refresh;
}


?>

<HTML><HEAD><TITLE>Reclinathon</TITLE>

<title>JavaScript Resources - A Stopwatch and Countdown Timer</title>
<script language="JavaScript">
<!-- Begin

var cmin,csec;

function Minutes(data) {

	return(Math.floor(data/60)); 
}

function Seconds(data) {

	return(data % 60); 
}

function Display(min,sec) {

	var disp;

	if(min<=9) disp=" 0";

	else disp=" ";

	disp+=min+":";

	if(sec<=9) disp+="0"+sec;

	else disp+=sec;

	return(disp); 
}

function Down() {

	cmin=1*Minutes(document.sw.beg2.value);

	csec=0+Seconds(document.sw.beg2.value);

	DownRepeat(); 
}

function DownRepeat() {
 
	  csec--;

	  if(csec==-1) { 
            csec=59; 
            cmin--; 

            if(cmin < 0) {
              csec = 0;
              cmin = 0;
            }

          }

	document.sw.disp2.value=Display(cmin,csec);

	if((cmin==0)&&(csec==0)) ;

	else down=setTimeout("DownRepeat()",1000); 
}

// End -->
</script>

<body bgcolor="#000000" onload="Down()"><CENTER>
<table width="1000" border="0">
  <tr>
    <td><div align="center"><font color="#FFFFFF" size="+5">La-Z-Dude Reclinathon: 
          Winter 2007</font></div></td>
  </tr>
  <tr>
    <td height="30" bgcolor="#000066">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0">
          <FORM NAME="sw">
          <input type="hidden" name="beg2" value="<?php echo $TIMER;?>">
          <tr>
            <td width="200"><img src="rec_left.jpg" width="200" height="200"></td>
            <td width="800"><div align="center">
                <font color="#FFFFFF" size="+7"><strong><input type="text" name="disp2" size="5" border="0" STYLE="background: Black; font-size: 28pt; color: #FFFFFF"></strong></font><BR><BR>
                <font color="#FFFFFF" size="+1"><?php if($TIMER > 0) { echo "Remaining Until the Next Movie:"; } else { echo "Now Playing:"; } ?></font><BR><BR>
                <font color="#FFFFFF" size="+7"><?php echo $NEXT_MOVIE; ?></font> </div></td>
            <td width="200"><img src="rec_right.jpg" width="200" height="200"></td>
          </tr>
          </FORM>
        </table></td>
  </tr>
  <tr>
    <td bgcolor="#003366">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><font color="#FFFFFF" size="+2"><BR><?php echo $row[1]; ?></font></div></td>
  </tr>
  <tr>
    <td><div align="center"><font color="#FFFFFF" size="+2"><BR><BR><?php echo $row[2]; ?></font></div></td>
  </tr>
</table>
</CENTER>

<meta http-equiv="refresh" content="<?php echo $refresh;?>">

</body>
</html>
