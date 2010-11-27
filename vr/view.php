<?php

$begin = time();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>The TV</title>
</head>
<body bgcolor="#ffffff" leftmargin = "0" topmargin = "0" marginwidth = "0" onunload="window.open('unload2.php?begin=<?php echo $begin; ?>','Unloader','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=600,height=450')">
<!--url's used in the movie-->
<!--text used in the movie-->
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="720" height="540" id="view" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="view.swf" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<embed src="view.swf" quality="high" bgcolor="#ffffff" width="720" height="540" name="view" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
</body>
</html>
