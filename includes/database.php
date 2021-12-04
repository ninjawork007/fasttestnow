<?php
      function dbCon() {
	$host =		"localhost";
        $user =		"ftnhealt_good";
        $passwd =	"+D5EEhD#9,K;";
        $db =		"ftnhealt_health";
        // $user =		"root";
        // $passwd =	"";
        // $db =		"ftnhealt_health";
        
        
				$con = mysqli_connect($host,$user,$passwd,$db);
				
				return $con;
			  }
               ?>