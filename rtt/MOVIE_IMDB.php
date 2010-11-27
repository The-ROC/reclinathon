<?php
require ("imdb/imdb.class.php");

class MOVIE_IMDB extends MOVIE
{
	public function DisplayForm()
	{
		echo "<FORM ACTION='imdb/imdbsearch.php' METHOD='get'>";
 		echo "<TABLE ALIGN='center' STYLE='margin-top:20px;border-collapse:collapse;' BORDER='1'>";
  		echo "<TR><TH COLSPAN='5' STYLE='background-color:#ffb000'>Search IMDB for...</TH></TR>";
  		echo "<TR><TD ALIGN='right' STYLE='padding-right:10px;padding-left:10px;'>Name:<BR>Type:</TD>";
     	echo "<TD><INPUT TYPE='text' NAME='name' SIZE='30' MAXLENGTH='50'><BR>";
        echo "<SELECT NAME='searchtype'><OPTION VALUE='movie'>Movie</OPTION><OPTION VALUE='episode'>Episode</OPTION><OPTION VALUE='nm'>Name</OPTION></SELECT></TD></TR>";
  		echo "<TR><TD ALIGN='right' STYLE='padding-right:10px;padding-left:10px;'>or IMDB ID:</TD><TD><INPUT TYPE='text' NAME='mid' SIZE='30' MAXLENGTH='7'></TD></TR>";
  		//echo "<TR><TD COLSPAN='2' ALIGN='center'><INPUT TYPE='submit' VALUE='Search'></TD></TR>";
		echo "<TR><TD> </TD><TD><INPUT TYPE='hidden' NAME='class' VALUE='MOVIE_IMDB'><INPUT TYPE='hidden' NAME='ObjectID' VALUE='" . $this->MovieID . "'><INPUT  TYPE='submit' VALUE='Submit'></TD></TR>";
		echo "</TABLE>";
		echo "</FORM>";
	}
	
	public function LoadFromForm()
	{
		
		$imdbID = $_GET['mid'];
		
		$imdbMovie = new imdb($imdbID);
		
		if(isset($_GET['ObjectId'])) {
			$this->MovieID = $_GET['ObjectId'];
		}
        $this->Title = $imdbMovie->title();
        
       	echo "<br />Title: " . $this->Title;
        $this->RunTime = $imdbMovie->runtime();
        echo "<br />RunTime: " . $this->RunTime;
        
        // TODO: Assign Genres
        // $imdbMovie->genres();
        $this->NumGenres = 0;
        $query = "SELECT * FROM GENRE";
        $result = $this->query($query);
        while($row = mysql_fetch_assoc($result))
        {
            if ($_POST["genre" . $row[GenreID]] != "")
            {
                $this->Genre += $row["Value"];
                $this->Genres[$this->NumGenres] = new GENRE($row["GenreID"], $row["Name"], $row["Canonical"]);
                $this->NumGenres++;
            }
        }
		
        // TODO: How to arrays work in PHP? Show list?  Just show first trailer?
        $trailerList = $imdbMovie->trailers();
        $this->TrailerLink = $trailerList[0];
        echo "<br /><br />Trailer: " . $this->TrailerLink;
        $this->IMDBLink = $imdbMovie->main_url();
        echo "<br /><br />IMDBLink: " . $this->IMDBLink;
        // TODO: Use Freshness rating instead of MPAA Star rating.
        $this->Freshness = $imdbMovie->rating();
        echo "<br /><br />Freshness: " . $this->Freshness;
        $this->Image = $imdbMovie->photo();
        echo "<br /><br />Image: " . $this->Image;
        
        // Other Info from API
        echo "<br /><br />Year: " . $imdbMovie->year();
        echo "<br /><br />Also Known As: ";
        print_r($imdbMovie->alsoknow());
        echo "<br /><br />Cast: ";
        $castMembers = $imdbMovie->cast();
        foreach($castMembers as $castMember) {
        	echo "<br />" . $castMember["name"] . " as " . $castMember["role"];
        }
        //echo "<br />Comment: " . $imdbMovie->comment();
        echo "<br /><br />Composer: ";
        print_r($imdbMovie->composer());
        echo "<br /><br />Director: ";
        print_r($imdbMovie->director());
        echo "<br /><br />Genres: ";
        print_r($imdbMovie->genres());
        echo "<br /><br />Trivia: ";
        $triviaQuestions = $imdbMovie->trivia();
        foreach($triviaQuestions as $triviaQuestion) {
        	echo "<br />" . $triviaQuestion;
        }
        echo "<br /><br />Tagline: " . $imdbMovie->tagline();
        echo "<br />";
	}
	
	public function ProcessForm()
	{
		$this->LoadFromForm();
		
		return 0;
	}
}