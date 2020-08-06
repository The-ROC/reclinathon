<?php
// A class for calculating a potential reclinee's Reclinathon Compatibility

class Compatibility {
	
	//private static $movieListMember = array();
	
	function getMovieList() {
		//if($movieListMember)
		//{
		//	return $movieListMember;
		//}
		
		//else
		//{
			$sql = "SELECT * FROM MOVIES";
			$movieListMember = mysqli_query($sql);
		//}
		
		return $movieListMember;
	}
}
?>