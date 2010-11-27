<?php

session_start();
include "layout.php";


//CHANGE THIS VARIABLE TO CHANGE WHICH QUIZ IS DISPLAYED
$session = "W2006";


//FETCH THE LIST OF QUESTIONS FROM THE DB, IF NECESSARY
if(!session_is_registered("QUESTIONS")) {
  $i = 0;
  $query = "SELECT * FROM Questions WHERE Session = \"".$session."\" ORDER BY Ordering";
  $result = mysql_query($query);
  while($QUESTIONS[$i] = mysql_fetch_row($result)) {
    $i++;
  }
  session_register("QUESTIONS");
  $NUM_QUESTIONS = $i;
  session_register("NUM_QUESTIONS");
  $CURRENT_QUESTION = 0;
  session_register("CURRENT_QUESTION");
}


//INITIALIZE THE GLOBAL SCORE VARIABLE
if(!session_is_registered("SCORE")) {
  $SCORE = 0;
  session_register("SCORE");
}


//IF A QUESTION HAS BEEN ANSWERED, UPDATE THE SCORE
if($answer != "") {
  if($type == "FILL") {
    if($ans == $answer) {
      $SCORE += $score;
    }
  }

  else if($type == "MC") {
    $SCORE += $answer;
  }

  else if($type == "SEL") {
    $query3 = "SELECT * FROM Choices WHERE QID = '".$qid."' ORDER BY Ordering";
    $result3 = mysql_query($query3);
    for($i = 0; $i < mysql_num_rows($result3); $i++) {
      $row3 = mysql_fetch_row($result3);
      if($answer[$i] == "1") {
        $SCORE += $row3[4];
      }
    }
  }

  else {
    echo "ERROR!!";
  }

    
  $CURRENT_QUESTION++;
}



//DISPLAY THE NEXT QUESTION, IF ONE EXISTS
if($CURRENT_QUESTION < $NUM_QUESTIONS) {
  $question = $QUESTIONS[$CURRENT_QUESTION][4];
  $type = $QUESTIONS[$CURRENT_QUESTION][2];
  $qid = $QUESTIONS[$CURRENT_QUESTION][0];
  $ans = $QUESTIONS[$CURRENT_QUESTION][5];
  $score = $QUESTIONS[$CURRENT_QUESTION][6];
  
  echo "<FORM ACTION='index.php' METHOD='POST'>";

  echo $question."<BR><BR>";

  //DISPLAY THE ANSWER BOX FOR A FILL_IN QUESTION
  if($type == "FILL") {
    echo "<INPUT TYPE='text' NAME='answer'>";
  }

  //DISPLAY THE LIST OF CHOICES FOR A MC OR SELECTION QUESTION
  else {
    $query2 = "SELECT * FROM Choices WHERE QID = '".$qid."' ORDER BY Ordering";
    $result2 = mysql_query($query2);
    
    echo "<TABLE WIDTH='50%'>";
    $i = 0;
    while($row2 = mysql_fetch_row($result2)) {
      echo "<TR><TD>";

      if($type == "MC") {
        echo "<INPUT TYPE='radio' NAME='answer' VALUE='".$row2[4]."'>";
      }
      else if($type == "SEL") {
        echo "<INPUT TYPE='checkbox' NAME='answer[".$i."]' VALUE='1'>";
      }
      else {
        echo "ERROR!";
      }

      echo "</TD><TD>".$row2[3]."</TD></TR>";
      $i++;
    }
    echo "</TABLE>"; 
  }

  echo "<BR><BR>";
  echo "<INPUT TYPE='SUBMIT' VALUE='Submit'>";
  echo "<INPUT TYPE='HIDDEN' NAME='qid' VALUE='".$qid."'>";
  echo "<INPUT TYPE='HIDDEN' NAME='type' VALUE='".$type."'>";
  echo "<INPUT TYPE='HIDDEN' NAME='ans' VALUE='".$ans."'>";
  echo "<INPUT TYPE='HIDDEN' NAME='score' VALUE='".$score."'>";
  echo "</FORM>";

}

//IF THE QUIZ IS OVER, GO TO THE REGISTRATION PAGE
else {
  echo "Your Score was: ".$SCORE;
  session_destroy();
  //include "register.php";
}
    
