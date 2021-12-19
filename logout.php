<!DOCTYPE HTML>
<html>
  <head>
    <title>Logout</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="css/myCSS.css">
  </head>
  <body> 
    
  <?php
		//include the header div, where authentication is checked and the navigation menu is placed.
  		require_once('cookies/header.php');
  ?>
	  
  <?php
        unset($_SESSION["username"]); 
        //SESSION é um array, unset limpa chave e valor mas o array existe. session_unset limpa tudo.
		
		//empty session array
		$_SESSION = array();                        #Estas duas linhas são iguais a um session_unset mas mais bruto 
		
		//send the user to the bye bye page.
		header("Location:login.php");
  ?>
	
  </body>
</html>