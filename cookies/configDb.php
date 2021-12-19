<?php

// Connect to DB; Query DB (CRUD); disconnect DB
function connectDB(){
	
	include('config.php');

	$db = mysqli_connect($host, $username, $password, $dbName);

	if (!$db) {
   	//connection error
   	return(mysqli_connect_error());
	}
	else{
		return($db);	
	}
}

//execute a query - with prepared statements this function is no longer necessary
/*function doQuery($db,$query){
	
	$result = mysqli_query($db, $query);
	
	if (!$result) {
   	//error executing the query
   	return(false);
	}
	else{
		return($result);	
	}
}*/

//close db connection
function closeDb($db){
	
	return(mysqli_close($db));
}

?>








