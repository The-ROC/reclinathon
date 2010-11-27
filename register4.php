<?php
session_start();
include "templateHead.html";
?>


<FONT COLOR = "white"><BR><BR>

<H1> Medical Evaluation </H1>

<H3> This section is a review of your medical history.  
Since reclinathon is a greuling event, we need to determine if you are in proper health to compete in the reclinathon. 
<BR><BR>
Please answer all of the following questions:
<BR><BR><CENTER>
<FORM ACTION="medicalReport.php" METHOD="post">
<TABLE WIDTH='60%' CELLSPACING='7'>
<TR><TD><FONT COLOR = "white">Do you have a heart condition?</TD><TD><SELECT NAME='heart'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Do you have low blood pressure?</TD><TD><SELECT NAME='blood'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Have you ever had an epileptic seizure?</TD><TD><SELECT NAME='seizure'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Do you have inflammatory bowel disease?</TD><TD><SELECT NAME='bowel'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Are you pregnant?</TD><TD><SELECT NAME='pregnant'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Do you have any handicaps or disabilities that would prevent you from sitting in the designated reclining position?</TD><TD><SELECT NAME='sitting'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Are you allergic to buffalo seasoned foods?</TD><TD><SELECT NAME='buffalo'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Do you experience frequent urges to talk loudly?</TD><TD><SELECT NAME='talking'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Please rate your level of bladder control from 1 to 10.<BR> (10 being highly controlled and 1 being on your way to buy some depends adult diapers right now)</TD><TD><INPUT TYPE='text' NAME='bladder' SIZE='2' MAXLENGTH='2'> </TD></TR>
<TR><TD><FONT COLOR = "white">Have you ever been put through a wood chipper?</TD><TD><SELECT NAME='chipper'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION></SELECT></TD></TR>
<TR><TD><FONT COLOR = "white">Have you ever experienced sudden death?</TD><TD><SELECT NAME='death'><OPTION VALUE='yes'>Yes</OPTION><OPTION VALUE='no'>No</OPTION><OPTION VALUE='Sometimes'>Sometimes</OPTION></SELECT></TD></TR>
</TABLE><BR>
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>